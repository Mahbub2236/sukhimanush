<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Security proof tests.
 *
 * Every test asserts the SECURE / EXPECTED behaviour.
 * A FAILING test here is evidence of a real vulnerability (not an opinion).
 */
class SecurityProofTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Never hit real SMS / AI / payment gateways from tests.
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

    private function makeDoctor(?User $user = null, float $fee = 500): Doctor
    {
        return Doctor::forceCreate([
            'user_id' => $user?->id,
            'name' => 'Dr QA '.Str::random(5),
            'slug' => 'dr-qa-'.Str::lower(Str::random(10)),
            'specialization' => 'Medicine',
            'designation' => 'Consultant',
            'chamber_address' => 'Dhaka',
            'consultation_fee' => $fee,
            'is_approved' => true,
        ]);
    }

    private function makeAppointment(Doctor $doctor, ?User $patient = null, array $extra = []): Appointment
    {
        return Appointment::forceCreate(array_merge([
            'appointment_number' => 'APT-QA-'.Str::upper(Str::random(8)),
            'doctor_id' => $doctor->id,
            'patient_id' => $patient?->id,
            'patient_name' => 'Secret Patient Name',
            'patient_email' => 'victim@test.local',
            'patient_phone' => '01711000000',
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00 AM',
            'serial_number' => 1,
            'consultation_fee' => $doctor->consultation_fee,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'reason_for_visit' => 'Confidential diagnosis',
        ], $extra));
    }

    /* ------------------------------------------------------------------
     | SEC-01  /api/image-proxy must not read local server files (LFI/SSRF)
     * -----------------------------------------------------------------*/
    public function test_sec01_image_proxy_cannot_read_local_files(): void
    {
        $response = $this->get('/api/image-proxy?url='.urlencode(base_path('composer.json')));

        $this->assertStringNotContainsString(
            'laravel/laravel',
            (string) $response->getContent(),
            'VULNERABLE: unauthenticated user can read server files (e.g. .env) via /api/image-proxy'
        );
    }

    /* ------------------------------------------------------------------
     | SEC-02  A forged bKash webhook must not mark an appointment paid
     |         when no webhook secret is configured (any non-production env)
     * -----------------------------------------------------------------*/
    public function test_sec02_forged_bkash_webhook_without_secret_does_not_mark_paid(): void
    {
        config(['services.bkash.secret' => null, 'services.bkash.webhook_secret' => null]);
        $apt = $this->makeAppointment($this->makeDoctor());

        $this->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $apt->appointment_number,
            'transactionStatus' => 'Completed',
            'trxID' => 'FAKE'.Str::random(6),
            'amount' => 1,
        ]);

        $this->assertNotSame('paid', $apt->fresh()->payment_status,
            'VULNERABLE: anyone can mark an appointment PAID with a forged webhook (no signature / gateway verification)');
    }

    /* ------------------------------------------------------------------
     | SEC-03  Paid amount lower than the consultation fee must be rejected
     * -----------------------------------------------------------------*/
    public function test_sec03_bkash_webhook_rejects_amount_mismatch(): void
    {
        config(['services.bkash.secret' => 'qa-secret']);
        $apt = $this->makeAppointment($this->makeDoctor(null, 800));

        $this->withHeaders(['X-Webhook-Secret' => 'qa-secret'])
            ->postJson('/api/payments/webhook/bkash', [
                'merchantInvoiceNumber' => $apt->appointment_number,
                'transactionStatus' => 'Completed',
                'trxID' => 'TRX'.Str::random(8),
                'amount' => 1,
            ]);

        $this->assertNotSame('paid', $apt->fresh()->payment_status,
            'VULNERABLE: 1 BDT payment marks an 800 BDT appointment as PAID (no amount verification)');
    }

    /* ------------------------------------------------------------------
     | SEC-04  Re-using one transaction ID for a second appointment must not
     |         leave that appointment PAID without a payment record
     * -----------------------------------------------------------------*/
    public function test_sec04_reused_trx_id_does_not_create_paid_appointment_without_payment(): void
    {
        config(['services.bkash.secret' => 'qa-secret']);
        $doctor = $this->makeDoctor(null, 500);
        $a1 = $this->makeAppointment($doctor);
        $a2 = $this->makeAppointment($doctor, null, ['serial_number' => 2]);
        $trx = 'TRX'.Str::upper(Str::random(8));

        foreach ([$a1, $a2] as $apt) {
            $this->withHeaders(['X-Webhook-Secret' => 'qa-secret'])
                ->postJson('/api/payments/webhook/bkash', [
                    'merchantInvoiceNumber' => $apt->appointment_number,
                    'transactionStatus' => 'Completed',
                    'trxID' => $trx,
                    'amount' => 500,
                ]);
        }

        $a2 = $a2->fresh();
        $hasPayment = Payment::where('appointment_id', $a2->id)->exists();
        $this->assertFalse($a2->payment_status === 'paid' && ! $hasPayment,
            'VULNERABLE: replayed trxID leaves appointment PAID with no payment row (writes are not in a DB transaction)');
        $this->assertNotSame('paid', $a2->payment_status,
            'VULNERABLE: one bKash transaction ID was accepted for two different appointments (replay)');
    }

    /* ------------------------------------------------------------------
     | SEC-05  Public queue-token endpoint must not leak patient data
     * -----------------------------------------------------------------*/
    public function test_sec05_queue_token_endpoint_does_not_leak_patient_data_to_anonymous(): void
    {
        $apt = $this->makeAppointment($this->makeDoctor());

        $response = $this->getJson("/api/queue/token/{$apt->id}");

        $this->assertStringNotContainsString('01711000000', (string) $response->getContent(),
            'VULNERABLE: anonymous user gets patient phone by enumerating sequential appointment IDs');
        $this->assertStringNotContainsString('Confidential diagnosis', (string) $response->getContent(),
            'VULNERABLE: anonymous user gets reason_for_visit (medical data)');
    }

    /* ------------------------------------------------------------------
     | SEC-06  Doctor A must not control Doctor B's live queue
     * -----------------------------------------------------------------*/
    public function test_sec06_doctor_cannot_call_next_on_another_doctors_queue(): void
    {
        $userA = $this->makeUser('doctor');
        $this->makeDoctor($userA);
        $doctorB = $this->makeDoctor($this->makeUser('doctor'));
        $this->makeAppointment($doctorB, null, ['status' => 'confirmed']);

        $response = $this->actingAs($userA, 'sanctum')
            ->postJson("/api/queue/call-next/{$doctorB->id}");

        $this->assertContains($response->status(), [403, 404],
            "VULNERABLE: doctor A changed doctor B's queue (HTTP {$response->status()})");
    }

    public function test_sec06b_doctor_cannot_trigger_emergency_bypass_on_another_doctor(): void
    {
        $userA = $this->makeUser('doctor');
        $this->makeDoctor($userA);
        $doctorB = $this->makeDoctor($this->makeUser('doctor'));

        $response = $this->actingAs($userA, 'sanctum')
            ->postJson("/api/doctor-portal/queue/emergency-bypass/{$doctorB->id}", [
                'action' => 'start',
                'patient_name' => 'X',
                'emergency_reason' => 'Heart Attack',
            ]);

        $this->assertContains($response->status(), [403, 404],
            "VULNERABLE: doctor A triggered emergency bypass on doctor B (HTTP {$response->status()})");
    }

    /* ------------------------------------------------------------------
     | SEC-07  Uploaded medical reports must not be stored in public/
     * -----------------------------------------------------------------*/
    public function test_sec07_patient_report_is_not_stored_in_public_directory(): void
    {
        $patient = $this->makeUser('patient');
        $before = File::exists(public_path('uploads/reports')) ? File::files(public_path('uploads/reports')) : [];

        $response = $this->actingAs($patient, 'sanctum')->post('/api/patient-portal/reports/upload', [
            'report_file' => UploadedFile::fake()->create('cbc.pdf', 20, 'application/pdf'),
            'report_title' => 'CBC',
        ], ['Accept' => 'application/json']);

        $after = File::exists(public_path('uploads/reports')) ? File::files(public_path('uploads/reports')) : [];
        $newFiles = array_diff(array_map('strval', $after), array_map('strval', $before));
        foreach ($newFiles as $f) {
            @unlink($f); // clean up the test artefact
        }

        $this->assertSame(201, $response->status(), 'Upload itself failed: '.$response->getContent());
        $this->assertEmpty($newFiles,
            'VULNERABLE: medical report written to public/uploads/reports (readable by anyone with the URL)');
    }

    /* ------------------------------------------------------------------
     | SEC-08  Appointment tracking by phone should not expose medical data
     * -----------------------------------------------------------------*/
    public function test_sec08_tracking_by_phone_does_not_expose_reason_for_visit(): void
    {
        $this->makeAppointment($this->makeDoctor(), null, ['patient_phone' => '01799887766']);

        $response = $this->getJson('/api/appointments/track?query=01799887766');

        $this->assertStringNotContainsString('Confidential diagnosis', (string) $response->getContent(),
            'VULNERABLE: anyone who knows a phone number sees that person\'s visit reasons');
    }

    /* ------------------------------------------------------------------
     | SEC-09  Doctor portfolio payload must sanitize HTML to prevent Stored XSS
     * -----------------------------------------------------------------*/
    public function test_sec09_doctor_portfolio_rejects_or_sanitizes_xss_payload(): void
    {
        $user = $this->makeUser('doctor');
        $doctor = $this->makeDoctor($user);

        $xss = '<img src=x onerror=alert(1)>';
        $this->actingAs($user, 'sanctum')->postJson('/api/doctor-portal/portfolio', [
            'portfolio_data' => [
                'hero' => [
                    'stamp_title' => $xss,
                ],
            ],
        ])->assertStatus(200);

        $doctor->refresh();
        $savedTitle = $doctor->portfolio_data['hero']['stamp_title'] ?? '';
        $this->assertStringNotContainsString('<img', $savedTitle,
            'VULNERABLE: Stored XSS payload is saved as-is in portfolio_data and rendered via v-html in TemplateDarkPulse.vue');
    }

    /* ------------------------------------------------------------------
     | Controls that SHOULD already pass (regression guards)
     * -----------------------------------------------------------------*/
    public function test_ctrl_public_registration_cannot_self_assign_admin(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Evil User', 'email' => 'evil_'.Str::random(6).'@test.local',
            'password' => 'secret123', 'role' => 'super_admin',
        ])->assertStatus(422);
    }

    public function test_ctrl_registered_doctor_is_not_auto_approved(): void
    {
        $email = 'newdoc_'.Str::random(6).'@test.local';
        $this->postJson('/api/auth/register', [
            'name' => 'New Doctor', 'email' => $email, 'password' => 'secret123', 'role' => 'doctor',
            'specialization' => 'Medicine', 'designation' => 'MO', 'chamber_address' => 'Dhaka',
        ]);
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertFalse((bool) $user->is_approved);
    }

    public function test_ctrl_patient_cannot_open_doctor_portal(): void
    {
        $this->actingAs($this->makeUser('patient'), 'sanctum')
            ->getJson('/api/doctor-portal/dashboard')->assertStatus(403);
    }

    public function test_ctrl_unapproved_doctor_is_blocked(): void
    {
        $this->actingAs($this->makeUser('doctor', ['is_approved' => false]), 'sanctum')
            ->getJson('/api/doctor-portal/dashboard')->assertStatus(403);
    }

    public function test_ctrl_login_is_rate_limited(): void
    {
        $last = null;
        for ($i = 0; $i < 17; $i++) {
            $last = $this->postJson('/api/auth/login', ['email' => 'x@test.local', 'password' => 'wrong']);
        }
        $last->assertStatus(429);
    }
}
