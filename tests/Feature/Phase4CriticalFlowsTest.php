<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase4CriticalFlowsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
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

    private function makeDoctor(?User $user = null): Doctor
    {
        $doctorUser = $user ?: $this->makeUser('doctor');

        return Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'Dr. Test Phase4 Specialist',
            'slug' => 'dr-phase4-'.Str::random(6),
            'specialization' => 'Cardiology',
            'designation' => 'Associate Professor',
            'bmdc_number' => 'BMDC-'.rand(10000, 99999),
            'email' => $doctorUser->email,
            'consultation_fee' => 1000,
            'phone' => '01712000000',
            'is_active' => true,
            'city' => 'Dhaka',
            'district' => 'Dhaka',
            'free_consultation_enabled' => true,
            'free_days' => ['Friday'],
            'free_patient_limit' => 1,
        ]);
    }

    /**
     * 1. Booking, Slot Availability & Collision Prevention.
     */
    public function test_slot_availability_and_double_booking_prevention(): void
    {
        $doctor = $this->makeDoctor();
        $targetDate = now()->addDays(5)->format('Y-m-d');
        $slotTime = '10:00 AM';

        // 1. Initial booking succeeds
        $res1 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Patient Alpha',
            'patient_email' => 'alpha@test.local',
            'patient_phone' => '01711111111',
            'appointment_date' => $targetDate,
            'appointment_time' => $slotTime,
            'consultation_type' => 'in_chamber',
        ]);
        $res1->assertStatus(201);
        $this->assertTrue($res1->json('success'));

        // 2. Second booking for the same slot is rejected with 422
        $res2 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Patient Beta',
            'patient_email' => 'beta@test.local',
            'patient_phone' => '01711111112',
            'appointment_date' => $targetDate,
            'appointment_time' => $slotTime,
            'consultation_type' => 'in_chamber',
        ]);
        $res2->assertStatus(422);
        $this->assertFalse($res2->json('success'));
        $this->assertStringContainsString('just booked by another patient', $res2->json('message'));
    }

    /**
     * 2. Free Consultation Quota Exhaustion.
     */
    public function test_free_consultation_quota_exhaustion(): void
    {
        $doctor = $this->makeDoctor();
        // Find next Friday (5 = Friday in Carbon)
        $nextFriday = now()->next(5)->format('Y-m-d');

        // 1st free booking takes the only quota (limit is 1)
        $res1 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Free Patient 1',
            'patient_email' => 'free1@test.local',
            'patient_phone' => '01711222221',
            'appointment_date' => $nextFriday,
            'appointment_time' => '09:00 AM',
            'consultation_type' => 'in_chamber',
        ]);
        $res1->assertStatus(201);
        $this->assertTrue((bool) $res1->json('appointment.is_free_consultation'));

        // 2nd booking on the same free day is rejected due to quota exhaustion
        $res2 = $this->postJson('/api/appointments', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Free Patient 2',
            'patient_email' => 'free2@test.local',
            'patient_phone' => '01711222222',
            'appointment_date' => $nextFriday,
            'appointment_time' => '09:30 AM',
            'consultation_type' => 'in_chamber',
        ]);
        $res2->assertStatus(422);
        $this->assertStringContainsString('কোটা', $res2->json('message'));
    }

    /**
     * 3. Payment Gateways: bKash (Webhook Security, Currency, Underpay, Replay, Success).
     */
    public function test_bkash_webhook_comprehensive_lifecycle(): void
    {
        config(['services.bkash.webhook_secret' => 'test-bkash-secret-key']);
        $doctor = $this->makeDoctor();

        $appointment = Appointment::create([
            'appointment_number' => 'APT-BKASH-'.rand(10000, 99999),
            'doctor_id' => $doctor->id,
            'patient_name' => 'bKash Payer',
            'patient_email' => 'bkash@test.local',
            'patient_phone' => '01711333333',
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '10:00 AM',
            'serial_number' => 1,
            'current_serving_serial' => 1,
            'consultation_fee' => 1000,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'consultation_type' => 'in_chamber',
        ]);

        // 1. Missing secret -> 403 Forbidden
        $resNoSecret = $this->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
        ]);
        $resNoSecret->assertStatus(403);

        $headers = ['X-Webhook-Secret' => 'test-bkash-secret-key'];

        // 2. Currency mismatch (USD instead of BDT) -> 422
        $resBadCurrency = $this->withHeaders($headers)->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
            'trxID' => 'TRX'.Str::random(10),
            'transactionStatus' => 'COMPLETED',
            'currency' => 'USD',
            'amount' => 1000,
        ]);
        $resBadCurrency->assertStatus(422);

        // 3. Underpayment (500 instead of 1000) -> 422
        $resUnderpay = $this->withHeaders($headers)->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
            'trxID' => 'TRX'.Str::random(10),
            'transactionStatus' => 'COMPLETED',
            'currency' => 'BDT',
            'amount' => 500,
        ]);
        $resUnderpay->assertStatus(422);

        // 4. Valid payment success -> 200 and marked paid
        $validTrx = 'TRX_SUCCESS_'.Str::random(8);
        $resSuccess = $this->withHeaders($headers)->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
            'trxID' => $validTrx,
            'transactionStatus' => 'COMPLETED',
            'currency' => 'BDT',
            'amount' => 1000,
        ]);
        $resSuccess->assertStatus(200);

        $appointment->refresh();
        $this->assertEquals('paid', $appointment->payment_status);
        $this->assertEquals('confirmed', $appointment->status);

        // 5. Replay / Idempotent duplicate -> 200 already completed
        $resReplay = $this->withHeaders($headers)->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
            'trxID' => $validTrx,
            'transactionStatus' => 'COMPLETED',
            'currency' => 'BDT',
            'amount' => 1000,
        ]);
        $resReplay->assertStatus(200);
        $this->assertStringContainsString('already completed', $resReplay->json('message'));
    }

    /**
     * 4. Payment Gateways: Nagad & SSLCommerz IPN.
     */
    public function test_nagad_and_sslcommerz_gateways(): void
    {
        config(['services.nagad.webhook_secret' => 'nagad-secret-123']);
        config(['services.sslcommerz.webhook_secret' => 'ssl-secret-123']);

        $doctor = $this->makeDoctor();

        // 1. Nagad flow
        $aptNagad = Appointment::create([
            'appointment_number' => 'NAGAD-'.rand(10000, 99999),
            'doctor_id' => $doctor->id,
            'patient_name' => 'Nagad Payer',
            'patient_email' => 'nagad@test.local',
            'patient_phone' => '01711444444',
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '11:00 AM',
            'serial_number' => 2,
            'current_serving_serial' => 1,
            'consultation_fee' => 800,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'consultation_type' => 'in_chamber',
        ]);

        $resNagad = $this->withHeaders(['X-Webhook-Secret' => 'nagad-secret-123'])
            ->postJson('/api/payments/webhook/nagad', [
                'order_id' => $aptNagad->appointment_number,
                'payment_ref_id' => 'NAGAD_TRX_'.Str::random(8),
                'status' => 'SUCCESS',
                'currency' => 'BDT',
                'amount' => 800,
            ]);
        $resNagad->assertStatus(200);
        $aptNagad->refresh();
        $this->assertEquals('paid', $aptNagad->payment_status);

        // 2. SSLCommerz flow
        $aptSsl = Appointment::create([
            'appointment_number' => 'SSL-'.rand(10000, 99999),
            'doctor_id' => $doctor->id,
            'patient_name' => 'SSL Payer',
            'patient_email' => 'ssl@test.local',
            'patient_phone' => '01711555555',
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '12:00 PM',
            'serial_number' => 3,
            'current_serving_serial' => 1,
            'consultation_fee' => 1200,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'consultation_type' => 'in_chamber',
        ]);

        $resSsl = $this->withHeaders(['X-Webhook-Secret' => 'ssl-secret-123'])
            ->postJson('/api/payments/webhook/sslcommerz', [
                'tran_id' => $aptSsl->appointment_number,
                'val_id' => 'VAL_'.Str::random(10),
                'status' => 'VALID',
                'currency' => 'BDT',
                'amount' => 1200,
            ]);
        $resSsl->assertStatus(200);
        $aptSsl->refresh();
        $this->assertEquals('paid', $aptSsl->payment_status);
    }

    /**
     * 5. Queue Operations: Doctor calls next patient, emergency bypass, patient tracking.
     */
    public function test_queue_operations_doctor_and_patient(): void
    {
        $doctorUser = $this->makeUser('doctor');
        $doctor = $this->makeDoctor($doctorUser);
        $today = now()->format('Y-m-d');

        $apt1 = Appointment::create([
            'appointment_number' => 'QUEUE-01',
            'doctor_id' => $doctor->id,
            'patient_name' => 'Queue Patient 1',
            'patient_email' => 'q1@test.local',
            'patient_phone' => '01711666661',
            'appointment_date' => $today,
            'appointment_time' => '09:00 AM',
            'serial_number' => 1,
            'current_serving_serial' => 1,
            'consultation_fee' => 1000,
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'is_called' => false,
            'consultation_type' => 'in_chamber',
        ]);

        // Doctor calls next patient
        $resCall = $this->actingAs($doctorUser, 'sanctum')
            ->postJson("/api/doctor-portal/queue/call-next/{$doctor->id}", [
                'date' => $today,
            ]);
        $resCall->assertStatus(200);
        $this->assertTrue($resCall->json('success'));
        $apt1->refresh();
        $this->assertTrue((bool) $apt1->is_called);

        // Patient tracks token status
        $patientUser = $this->makeUser('patient', ['email' => 'q1@test.local']);
        $apt1->update(['patient_id' => $patientUser->id]);

        $resTrack = $this->actingAs($patientUser, 'sanctum')
            ->getJson("/api/patient-portal/queue/token/{$apt1->id}");
        $resTrack->assertStatus(200);
        $this->assertTrue($resTrack->json('success'));
        $this->assertEquals(1, $resTrack->json('my_serial'));

        // Emergency bypass triggered by doctor
        $resBypass = $this->actingAs($doctorUser, 'sanctum')
            ->postJson("/api/doctor-portal/queue/emergency-bypass/{$doctor->id}", [
                'action' => 'start',
                'emergency_reason' => 'Acute cardiac distress',
                'patient_name' => 'Emergency ICU Patient',
            ]);
        $resBypass->assertStatus(200);
        $this->assertTrue($resBypass->json('success'));
    }

    /**
     * 6. Prescriptions: Doctor creates prescription & Patient views it.
     */
    public function test_prescription_lifecycle_and_authorization(): void
    {
        $doctorUser = $this->makeUser('doctor');
        $doctor = $this->makeDoctor($doctorUser);
        $patientUser = $this->makeUser('patient');

        $appointment = Appointment::create([
            'appointment_number' => 'RX-APT-'.rand(1000, 9999),
            'doctor_id' => $doctor->id,
            'patient_id' => $patientUser->id,
            'patient_name' => $patientUser->name,
            'patient_email' => $patientUser->email,
            'patient_phone' => '01711777777',
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00 AM',
            'serial_number' => 1,
            'current_serving_serial' => 1,
            'consultation_fee' => 1000,
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'consultation_type' => 'in_chamber',
        ]);

        // Doctor writes prescription
        $resRx = $this->actingAs($doctorUser, 'sanctum')
            ->postJson('/api/doctor-portal/prescriptions', [
                'appointment_id' => $appointment->id,
                'patient_name' => $patientUser->name,
                'patient_age_gender' => '35 / Male',
                'diagnosis' => 'Hypertension Stage 1',
                'medicines' => [
                    [
                        'name' => 'Tab. Napa 500mg (Paracetamol)',
                        'dosage' => '1 + 0 + 1',
                        'duration' => '5 days',
                        'instruction' => 'After meal',
                    ],
                ],
                'advice' => 'Low salt diet, 30 min daily walking',
            ]);

        $resRx->assertStatus(200);
        $this->assertTrue($resRx->json('success'));

        // Appointment is automatically completed
        $appointment->refresh();
        $this->assertEquals('completed', $appointment->status);

        // Patient can view their prescription (returns JSON array)
        $resPatientRx = $this->actingAs($patientUser, 'sanctum')
            ->getJson('/api/patient-portal/my-prescriptions');
        $resPatientRx->assertStatus(200);
        $this->assertCount(1, $resPatientRx->json());

        // Another patient cannot see this prescription
        $otherPatient = $this->makeUser('patient');
        $resOther = $this->actingAs($otherPatient, 'sanctum')
            ->getJson('/api/patient-portal/my-prescriptions');
        $resOther->assertStatus(200);
        $this->assertCount(0, $resOther->json());
    }

    /**
     * 7. Admin Actions & Role Enforcement.
     */
    public function test_admin_actions_and_role_protection(): void
    {
        $adminUser = $this->makeUser('admin');
        $patientUser = $this->makeUser('patient');
        $doctor = $this->makeDoctor();

        // 1. Patient trying to access admin stats -> 403
        $resForbidden = $this->actingAs($patientUser, 'sanctum')
            ->getJson('/api/admin/stats');
        $resForbidden->assertStatus(403);

        // 2. Admin successfully accesses stats -> 200
        $resStats = $this->actingAs($adminUser, 'sanctum')
            ->getJson('/api/admin/stats');
        $resStats->assertStatus(200);
        $this->assertArrayHasKey('total_revenue', $resStats->json());

        // 3. Admin toggles doctor approval status
        $doctor->update(['is_approved' => false]);
        $resToggle = $this->actingAs($adminUser, 'sanctum')
            ->patchJson("/api/admin/doctors/{$doctor->id}/status", [
                'is_approved' => true,
            ]);
        $resToggle->assertStatus(200);
        $doctor->refresh();
        $this->assertTrue((bool) $doctor->is_approved);
    }

    /**
     * 8. Emergency / Blood SOS Radar & Donors.
     */
    public function test_blood_sos_radar_and_donor_registration(): void
    {
        // 1. Post a new blood request
        $resRequest = $this->postJson('/api/blood-sos/requests', [
            'patient_name' => 'Emergency Anemia Patient',
            'blood_group' => 'O+',
            'component' => 'whole_blood',
            'units_needed' => 2,
            'hospital_name' => 'Dhaka Medical College Hospital',
            'district' => 'Dhaka',
            'contact_name' => 'Rahim Uddin',
            'contact_phone' => '01711888888',
            'urgency' => 'critical_immediate',
        ]);
        $resRequest->assertStatus(200);
        $this->assertTrue($resRequest->json('success'));

        // 2. List blood requests
        $resList = $this->getJson('/api/blood-sos/requests?blood_group=O+&district=Dhaka');
        $resList->assertStatus(200);
        $this->assertTrue($resList->json('success'));

        // 3. Register as a blood donor
        $resDonor = $this->postJson('/api/blood-sos/donors/register', [
            'name' => 'Kind Donor',
            'blood_group' => 'O+',
            'district' => 'Dhaka',
            'phone' => '01711999999',
            'is_available' => true,
        ]);
        $resDonor->assertStatus(200);
        $this->assertTrue($resDonor->json('success'));
    }
}
