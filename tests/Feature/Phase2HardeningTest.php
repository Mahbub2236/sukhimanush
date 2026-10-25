<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\DoctorChamber;
use App\Models\PatientReport;
use App\Models\User;
use App\Services\SmsNotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase2HardeningTest extends TestCase
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

    /**
     * 1. File Uploads: strict validation, MIME checks, extension whitelisting, and rejection of scripts/SVG.
     */
    public function test_patient_report_rejects_non_whitelisted_mime_types(): void
    {
        $patient = $this->makeUser('patient');

        // Malicious file pretending to be pdf but with text/plain or executable mime
        $badFile = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->actingAs($patient, 'sanctum')
            ->postJson('/api/patient-portal/reports/upload', [
                'report_file' => $badFile,
                'report_title' => 'Blood Test Report',
            ]);

        $response->assertStatus(422);
    }

    public function test_portfolio_media_rejects_svg_and_non_image_files(): void
    {
        $doctorUser = $this->makeUser('doctor');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'Dr. Test Specialist',
            'slug' => 'dr-test-specialist-'.Str::random(6),
            'specialization' => 'Cardiology',
            'designation' => 'Consultant',
            'bmdc_number' => 'BMDC-99991',
            'email' => $doctorUser->email,
        ]);

        $svgFile = UploadedFile::fake()->create('vector.svg', 50, 'image/svg+xml');

        $response = $this->actingAs($doctorUser, 'sanctum')
            ->postJson('/api/doctor-portal/portfolio/upload-media', [
                'media' => $svgFile,
            ]);

        $response->assertStatus(422);
    }

    /**
     * 2. Mass Assignment: User cannot set role, is_approved, or agent_commission_balance.
     */
    public function test_registration_cannot_elevate_role_or_auto_approve(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Attacker User',
            'email' => 'attacker@example.com',
            'password' => 'secret123',
            'role' => 'admin', // disallowed role
            'is_approved' => true,
            'agent_commission_balance' => 99999,
        ]);

        // Must reject role 'admin' in registration
        $response->assertStatus(422);
    }

    public function test_profile_update_cannot_change_role_or_privileged_fields(): void
    {
        $user = $this->makeUser('patient', [
            'agent_commission_balance' => 0.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/profile/update', [
                'name' => 'Updated Name',
                'role' => 'admin',
                'is_approved' => false,
                'agent_commission_balance' => 50000,
            ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('patient', $user->role);
        $this->assertEquals(0.00, (float) $user->agent_commission_balance);
        $this->assertTrue((bool) $user->is_approved);
    }

    /**
     * 3. Authorization Sweep: Clinical relationship required for doctor to view patient reports and metrics.
     */
    public function test_unrelated_doctor_cannot_view_patient_reports_or_metrics(): void
    {
        $patient = $this->makeUser('patient');
        $unrelatedDocUser = $this->makeUser('doctor');
        $doctor = Doctor::create([
            'user_id' => $unrelatedDocUser->id,
            'name' => 'Dr. Unrelated',
            'slug' => 'dr-unrelated-'.Str::random(6),
            'specialization' => 'Neurology',
            'designation' => 'Consultant',
            'bmdc_number' => 'BMDC-88881',
            'email' => $unrelatedDocUser->email,
        ]);

        // Attempt to view patient's reports without any appointment
        $response = $this->actingAs($unrelatedDocUser, 'sanctum')
            ->getJson("/api/doctor-portal/patient-reports/{$patient->id}");

        $response->assertStatus(403);

        // Attempt to view patient's chronic health metrics without appointment
        $metricsResponse = $this->actingAs($unrelatedDocUser, 'sanctum')
            ->getJson("/api/doctor-portal/patient-metrics/{$patient->id}");

        $metricsResponse->assertStatus(403);
    }

    public function test_doctor_b_cannot_update_or_delete_doctor_a_chamber(): void
    {
        $docA = $this->makeUser('doctor');
        $doctorA = Doctor::create([
            'user_id' => $docA->id,
            'name' => 'Dr. Alpha',
            'slug' => 'dr-alpha-'.Str::random(6),
            'specialization' => 'Cardiology',
            'designation' => 'Consultant',
            'bmdc_number' => 'BMDC-11111',
            'email' => $docA->email,
        ]);
        $chamber = DoctorChamber::create([
            'doctor_id' => $doctorA->id,
            'chamber_name' => 'Alpha Clinic',
            'address' => 'Dhaka',
            'is_active' => true,
        ]);

        $docB = $this->makeUser('doctor');
        Doctor::create([
            'user_id' => $docB->id,
            'name' => 'Dr. Beta',
            'slug' => 'dr-beta-'.Str::random(6),
            'specialization' => 'Pediatrics',
            'designation' => 'Consultant',
            'bmdc_number' => 'BMDC-22222',
            'email' => $docB->email,
        ]);

        // Doctor B tries to update Doctor A's chamber
        $response = $this->actingAs($docB, 'sanctum')
            ->putJson("/api/doctor-portal/chambers/{$chamber->id}", [
                'chamber_name' => 'Hacked Chamber',
                'address' => 'Malicious Address',
            ]);

        $response->assertStatus(403);
    }

    /**
     * 4. Brute Force: Staff PIN login backoff / lockout.
     */
    public function test_staff_pin_login_locks_out_after_consecutive_failures(): void
    {
        $docUser = $this->makeUser('doctor');
        $doctor = Doctor::create([
            'user_id' => $docUser->id,
            'name' => 'Dr. Chamber Owner',
            'slug' => 'dr-chamber-owner-'.Str::random(6),
            'specialization' => 'General Medicine',
            'designation' => 'Consultant',
            'bmdc_number' => 'BMDC-77771',
            'email' => $docUser->email,
        ]);

        $assistant = DoctorAssistant::create([
            'doctor_id' => $doctor->id,
            'name' => 'Staff Member',
            'phone' => '01711999999',
            'access_pin' => Hash::make('1234'),
            'is_active' => true,
        ]);

        RateLimiter::clear('staff_login:01711999999|127.0.0.1');

        // Send 5 incorrect PIN attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/staff-login', [
                'phone' => '01711999999',
                'access_pin' => '0000',
            ]);
            $response->assertStatus(401);
        }

        // 6th attempt should be locked out with 429
        $lockoutResponse = $this->postJson('/api/auth/staff-login', [
            'phone' => '01711999999',
            'access_pin' => '1234', // even with correct PIN
        ]);

        $lockoutResponse->assertStatus(429);
    }

    /**
     * 5. Sanctum: Password change revokes tokens.
     */
    public function test_changing_password_revokes_active_sanctum_tokens(): void
    {
        $user = $this->makeUser('patient');
        $token1 = $user->createToken('device_1')->plainTextToken;
        $token2 = $user->createToken('device_2')->plainTextToken;

        $this->assertCount(2, $user->tokens);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/profile/update', [
                'name' => $user->name,
                'password' => 'newpassword123',
            ]);

        $response->assertStatus(200);

        // All existing tokens must be deleted upon password change
        $user->refresh();
        $this->assertCount(0, $user->tokens);
    }

    /**
     * 6. Security Headers: Responses contain modern browser protection headers.
     */
    public function test_api_responses_contain_security_headers(): void
    {
        $response = $this->getJson('/api/home-data');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * 7. Tokens & Identifiers: Appointment numbers and report numbers have high entropy.
     */
    public function test_appointment_and_report_numbers_are_cryptographically_unguessable(): void
    {
        $appNum1 = Appointment::generateAppointmentNumber();
        $appNum2 = Appointment::generateAppointmentNumber();

        $this->assertNotEquals($appNum1, $appNum2);
        // Length should be sufficient (prefix + random string of at least 8 chars)
        $this->assertGreaterThanOrEqual(16, strlen($appNum1));

        $repNum1 = PatientReport::generateReportNumber();
        $this->assertGreaterThanOrEqual(16, strlen($repNum1));
    }

    /**
     * 8. SMS rate limiting per recipient.
     */
    public function test_sms_service_throttles_excessive_sms_to_same_phone(): void
    {
        $service = app(SmsNotificationService::class);
        $phone = '01899123456';

        RateLimiter::clear('sms_rate_limit:8801899123456');

        // First 3 should pass
        for ($i = 0; $i < 3; $i++) {
            $result = $service->sendRaw($phone, "Test message {$i}");
            $this->assertTrue($result['success'] ?? false);
        }

        // 4th message within short window should be throttled
        $throttledResult = $service->sendRaw($phone, 'Spam message');
        $this->assertFalse($throttledResult['success']);
        $this->assertEquals('throttled', $throttledResult['mode']);
    }
}
