<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\PatientReport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase3QualityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
        Storage::fake('local');
    }

    private function makeUser(string $role, array $extra = []): User
    {
        return User::forceCreate(array_merge([
            'name' => 'QA '.$role,
            'email' => 'qa_'.$role.'_'.Str::random(8).'@test.local',
            'password' => Hash::make('password'),
            'role' => $role,
            'is_approved' => true,
        ], $extra));
    }

    private function makeDoctor(): Doctor
    {
        $doctorUser = $this->makeUser('doctor');

        return Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'Dr. Test Quality Specialist',
            'slug' => 'dr-quality-specialist-'.Str::random(6),
            'specialization' => 'Neurology',
            'designation' => 'Professor & Head',
            'bmdc_number' => 'BMDC-'.rand(10000, 99999),
            'email' => $doctorUser->email,
            'consultation_fee' => 1000,
            'phone' => '01700000000',
            'is_active' => true,
            'city' => 'Dhaka',
            'district' => 'Dhaka',
        ]);
    }

    /**
     * 1. BookAppointmentApiRequest validation & execution.
     */
    public function test_appointment_booking_request_validation(): void
    {
        // Empty payload
        $response = $this->postJson('/api/appointments', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'doctor_id',
                'patient_name',
                'patient_email',
                'patient_phone',
                'appointment_date',
                'appointment_time',
            ]);

        // Invalid date in past
        $doctor = $this->makeDoctor();
        $responsePast = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Rahim Uddin',
            'patient_email' => 'rahim@test.local',
            'patient_phone' => '01711223344',
            'appointment_date' => '2020-01-01',
            'appointment_time' => '10:00 AM',
        ]);
        $responsePast->assertStatus(422)
            ->assertJsonValidationErrors(['appointment_date']);
    }

    /**
     * 2. Concurrency: duplicate slot booking prevention.
     */
    public function test_concurrent_appointment_booking_prevents_slot_collision(): void
    {
        $doctor = $this->makeDoctor();
        $targetDate = now()->addDays(2)->format('Y-m-d');
        $targetTime = '11:00 AM';

        // First booking succeeds
        $res1 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Patient One',
            'patient_email' => 'p1@test.local',
            'patient_phone' => '01711000001',
            'appointment_date' => $targetDate,
            'appointment_time' => $targetTime,
            'consultation_type' => 'in_chamber',
        ]);
        $res1->assertStatus(201);
        $this->assertTrue($res1->json('success'));

        // Second booking for same doctor/date/time is rejected
        $res2 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Patient Two',
            'patient_email' => 'p2@test.local',
            'patient_phone' => '01711000002',
            'appointment_date' => $targetDate,
            'appointment_time' => $targetTime,
            'consultation_type' => 'in_chamber',
        ]);
        $res2->assertStatus(422);
        $this->assertFalse($res2->json('success'));
        $this->assertStringContainsString('just booked by another patient', $res2->json('message'));
    }

    /**
     * 3. Serial number generation order.
     */
    public function test_appointment_booking_allocates_sequential_serial_numbers(): void
    {
        $doctor = $this->makeDoctor();
        $targetDate = now()->addDays(3)->format('Y-m-d');

        $res1 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Serial Patient A',
            'patient_email' => 'spa@test.local',
            'patient_phone' => '01711000010',
            'appointment_date' => $targetDate,
            'appointment_time' => '02:00 PM',
        ]);
        $res1->assertStatus(201);
        $this->assertEquals(1, $res1->json('appointment.serial_number'));

        $res2 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Serial Patient B',
            'patient_email' => 'spb@test.local',
            'patient_phone' => '01711000020',
            'appointment_date' => $targetDate,
            'appointment_time' => '02:30 PM',
        ]);
        $res2->assertStatus(201);
        $this->assertEquals(2, $res2->json('appointment.serial_number'));
    }

    /**
     * 4. UploadReportRequest validation.
     */
    public function test_upload_report_request_validation(): void
    {
        $patient = $this->makeUser('patient');

        // Missing required fields
        $resEmpty = $this->actingAs($patient, 'sanctum')
            ->postJson('/api/patient-portal/reports/upload', []);
        $resEmpty->assertStatus(422)
            ->assertJsonValidationErrors(['report_file', 'report_title']);

        // Successful upload with valid JPEG
        $validFile = UploadedFile::fake()->image('chest_xray.jpg', 600, 600);
        $resSuccess = $this->actingAs($patient, 'sanctum')
            ->postJson('/api/patient-portal/reports/upload', [
                'report_file' => $validFile,
                'report_title' => 'Chest X-Ray Digital',
                'test_type' => 'xray',
                'diagnostic_center' => 'Square Hospital Ltd.',
                'patient_notes' => 'Routine checkup scan',
            ]);

        $resSuccess->assertStatus(201);
        $this->assertTrue($resSuccess->json('success'));
        $this->assertEquals('Chest X-Ray Digital', $resSuccess->json('report.report_title'));
        $this->assertEquals('image', $resSuccess->json('report.file_type'));
    }

    /**
     * 5. Patient reports single aggregate query stats.
     */
    public function test_patient_reports_aggregate_stats(): void
    {
        $patient = $this->makeUser('patient');

        // Create sample reports directly
        PatientReport::create([
            'report_number' => 'REP-TEST-1',
            'patient_id' => $patient->id,
            'report_title' => 'Blood Count 1',
            'file_path' => 'reports/test1.jpg',
            'file_type' => 'image',
            'file_size' => '1.2 MB',
            'status' => 'uploaded',
        ]);

        PatientReport::create([
            'report_number' => 'REP-TEST-2',
            'patient_id' => $patient->id,
            'report_title' => 'MRI Brain',
            'file_path' => 'reports/test2.pdf',
            'file_type' => 'pdf',
            'file_size' => '5.4 MB',
            'status' => 'reviewed',
        ]);

        $res = $this->actingAs($patient, 'sanctum')
            ->getJson('/api/patient-portal/my-reports');

        $res->assertStatus(200);
        $this->assertTrue($res->json('success'));
        $this->assertEquals(2, $res->json('counts.total'));
        $this->assertEquals(1, $res->json('counts.images'));
        $this->assertEquals(1, $res->json('counts.pdfs'));
        $this->assertEquals(1, $res->json('counts.reviewed'));
    }

    /**
     * 6. Doctor chamber store and update requests & primary sync.
     */
    public function test_doctor_chamber_store_and_update_validation(): void
    {
        $doctor = $this->makeDoctor();
        $doctorUser = $doctor->user;

        // Validation error on empty store
        $resEmpty = $this->actingAs($doctorUser, 'sanctum')
            ->postJson('/api/doctor-portal/chambers', []);
        $resEmpty->assertStatus(422)
            ->assertJsonValidationErrors(['chamber_name', 'address']);

        // Successful store
        $resStore = $this->actingAs($doctorUser, 'sanctum')
            ->postJson('/api/doctor-portal/chambers', [
                'chamber_name' => 'Apollo Medical Centre',
                'branch_name' => 'Dhanmondi Branch',
                'address' => 'House 12, Road 5, Dhanmondi, Dhaka',
                'consultation_fee' => 1200,
                'slot_duration_minutes' => 20,
            ]);
        $resStore->assertStatus(200);
        $chamberId = $resStore->json('chamber.id');
        $this->assertNotNull($chamberId);

        // Update chamber with UpdateDoctorChamberRequest
        $resUpdate = $this->actingAs($doctorUser, 'sanctum')
            ->putJson('/api/doctor-portal/chambers/'.$chamberId, [
                'chamber_name' => 'Evercare Premium Clinic',
                'address' => 'Plot 81, Block E, Bashundhara R/A, Dhaka',
                'consultation_fee' => 1500,
                'is_primary' => true,
            ]);
        $resUpdate->assertStatus(200);
        $this->assertEquals('Evercare Premium Clinic', $resUpdate->json('chamber.chamber_name'));
        $this->assertEquals(1500, (float) $resUpdate->json('chamber.consultation_fee'));
    }

    /**
     * 7. Composite performance indexes exist on production schema.
     */
    public function test_phase3_composite_database_indexes_exist(): void
    {
        $this->assertTrue(
            Schema::hasIndex('appointments', 'idx_doc_date_status'),
            'Index idx_doc_date_status must exist on appointments table'
        );

        $this->assertTrue(
            Schema::hasIndex('appointments', 'idx_patient_status'),
            'Index idx_patient_status must exist on appointments table'
        );

        $this->assertTrue(
            Schema::hasIndex('doctor_chambers', 'idx_doc_chamber_primary'),
            'Index idx_doc_chamber_primary must exist on doctor_chambers table'
        );

        $this->assertTrue(
            Schema::hasIndex('patient_reports', 'idx_patient_reports_created'),
            'Index idx_patient_reports_created must exist on patient_reports table'
        );
    }
}
