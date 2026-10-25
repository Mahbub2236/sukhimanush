<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientReport;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase1HardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
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

    private function makeDoctor(User $user, float $fee = 1000.00): Doctor
    {
        return Doctor::forceCreate([
            'user_id' => $user->id,
            'name' => $user->name,
            'slug' => 'dr-'.Str::lower(Str::random(8)),
            'specialization' => 'Cardiology',
            'designation' => 'Senior Consultant',
            'chamber_address' => 'Dhaka Medical Complex',
            'consultation_fee' => $fee,
            'is_approved' => true,
        ]);
    }

    private function makeAppointment(Doctor $doctor, User $patient, float $fee = 1000.00): Appointment
    {
        return Appointment::forceCreate([
            'appointment_number' => 'APT-'.strtoupper(Str::random(8)),
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '0171'.rand(1000000, 9999999),
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'consultation_fee' => $fee,
        ]);
    }

    /**
     * 1. SEC-07: Patient Reports migration command & rollback
     */
    public function test_reports_migrate_to_private_command_dry_run_and_execution_and_rollback(): void
    {
        $patient = $this->makeUser('patient');
        $publicDir = public_path('uploads/reports');
        if (! File::exists($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }

        $testFileName = 'test_report_'.uniqid().'.pdf';
        $publicFilePath = "{$publicDir}/{$testFileName}";
        File::put($publicFilePath, '%PDF-1.4 test medical report content');

        $report = PatientReport::forceCreate([
            'report_number' => 'REP-'.strtoupper(Str::random(6)),
            'patient_id' => $patient->id,
            'report_title' => 'Blood Test Report',
            'test_type' => 'blood_test',
            'report_date' => now()->format('Y-m-d'),
            'file_path' => "uploads/reports/{$testFileName}",
            'file_type' => 'pdf',
            'file_size' => '32 KB',
            'status' => 'uploaded',
        ]);

        // Dry-run mode: verify file remains in public and DB not modified
        $this->artisan('reports:migrate-to-private', ['--dry-run' => true])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($publicFilePath));
        $this->assertSame("uploads/reports/{$testFileName}", $report->fresh()->file_path);

        // Live execution: verify file moved to private storage and DB updated
        $this->artisan('reports:migrate-to-private')
            ->assertExitCode(0);

        $privateFilePath = storage_path("app/private/reports/{$testFileName}");
        $this->assertFalse(File::exists($publicFilePath), 'Public file must not exist after migration.');
        $this->assertTrue(File::exists($privateFilePath), 'File must exist in storage/app/private/reports.');
        $this->assertSame("reports/{$testFileName}", $report->fresh()->file_path);

        // Rollback: verify file is restored to public and DB reverted
        $this->artisan('reports:migrate-to-private', ['--rollback' => true])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($publicFilePath), 'File must be restored to public directory on rollback.');
        $this->assertSame("uploads/reports/{$testFileName}", $report->fresh()->file_path);

        // Cleanup
        if (File::exists($publicFilePath)) {
            File::delete($publicFilePath);
        }
        if (File::exists($privateFilePath)) {
            File::delete($privateFilePath);
        }
    }

    /**
     * 1b. SEC-07: Temporary signed URL & authorization for report stream/download
     */
    public function test_patient_report_download_requires_valid_signed_url_or_proper_role(): void
    {
        $patientA = $this->makeUser('patient');
        $patientB = $this->makeUser('patient');
        $doctorUser = $this->makeUser('doctor');
        $doctor = $this->makeDoctor($doctorUser, 1000);

        $privateReportsDir = storage_path('app/private/reports');
        if (! File::exists($privateReportsDir)) {
            File::makeDirectory($privateReportsDir, 0755, true);
        }

        $testFile = 'report_'.uniqid().'.pdf';
        File::put("{$privateReportsDir}/{$testFile}", '%PDF-1.4 sample content');

        $report = PatientReport::forceCreate([
            'report_number' => 'REP-'.strtoupper(Str::random(6)),
            'patient_id' => $patientA->id,
            'report_title' => 'CBC Test',
            'test_type' => 'blood_test',
            'report_date' => now()->format('Y-m-d'),
            'file_path' => "reports/{$testFile}",
            'file_type' => 'pdf',
            'file_size' => '20 KB',
            'status' => 'uploaded',
        ]);

        // 1. Unsigned, anonymous access must be 403 Forbidden
        $response = $this->get('/api/patient-reports/stream/'.$report->id);
        $response->assertStatus(403);

        // 2. Tampered signature must be 403 Forbidden
        $validSignedUrl = URL::temporarySignedRoute('patient.reports.download', now()->addMinutes(30), ['id' => $report->id]);
        $tamperedUrl = $validSignedUrl.'tampered';
        $this->get($tamperedUrl)->assertStatus(403);

        // 3. Valid signed URL works
        $this->get($validSignedUrl)->assertStatus(200);

        // 4. Authenticated Patient A can access their own report even without signed URL
        $this->actingAs($patientA, 'sanctum')
            ->get('/api/patient-reports/stream/'.$report->id)
            ->assertStatus(200);

        // 5. Authenticated Patient B cannot access Patient A's report
        $this->actingAs($patientB, 'sanctum')
            ->get('/api/patient-reports/stream/'.$report->id)
            ->assertStatus(403);

        // Cleanup
        if (File::exists("{$privateReportsDir}/{$testFile}")) {
            File::delete("{$privateReportsDir}/{$testFile}");
        }
    }

    /**
     * 2. SEC-01: Image Proxy Whitelist, MIME, Size, and SSRF restrictions
     */
    public function test_image_proxy_rejects_unwhitelisted_domains_and_ssrf(): void
    {
        // Unwhitelisted domain rejected
        $response = $this->get('/api/image-proxy?url='.urlencode('https://malicious-external-site.com/image.jpg'));
        $response->assertStatus(403);

        // Localhost / private IP SSRF rejected
        $response = $this->get('/api/image-proxy?url='.urlencode('http://127.0.0.1:8000/api/home-data'));
        $response->assertStatus(403);

        $fakeJpg = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB";
        Http::fake([
            'https://images.unsplash.com/malicious.svg' => Http::response('<script>alert("xss")</script>', 200, ['Content-Type' => 'text/html']),
            'https://images.unsplash.com/huge.jpg' => Http::response(str_repeat('A', 5242881), 200, ['Content-Type' => 'image/jpeg']),
            'https://images.unsplash.com/doctor.jpg' => Http::response($fakeJpg, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        // Whitelisted domain with non-image MIME rejected with 415
        $response = $this->get('/api/image-proxy?url='.urlencode('https://images.unsplash.com/malicious.svg'));
        $response->assertStatus(415);

        // Whitelisted domain exceeding 5MB rejected with 413
        $response = $this->get('/api/image-proxy?url='.urlencode('https://images.unsplash.com/huge.jpg'));
        $response->assertStatus(413);

        // Whitelisted domain with valid image passes with security headers
        $response = $this->get('/api/image-proxy?url='.urlencode('https://images.unsplash.com/doctor.jpg'));
        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * 3. SEC-02: Payment Webhooks (bKash, Nagad, SSLCommerz)
     */
    public function test_payment_webhooks_fail_closed_and_enforce_currency_fee_and_replay_protection(): void
    {
        $patient = $this->makeUser('patient');
        $doctorUser = $this->makeUser('doctor');
        $doctor = $this->makeDoctor($doctorUser, 1200);

        $appointment = $this->makeAppointment($doctor, $patient, 1200.00);

        // --- bKash Webhook Tests ---
        config(['services.bkash.webhook_secret' => 'test-bkash-secret-key']);

        // Missing secret -> 403
        $this->postJson('/api/payments/webhook/bkash', [
            'merchantInvoiceNumber' => $appointment->appointment_number,
            'amount' => 1200,
            'status' => 'COMPLETED',
        ])->assertStatus(403);

        // Wrong currency (USD instead of BDT) -> 422
        $this->withHeaders(['X-Webhook-Secret' => 'test-bkash-secret-key'])
            ->postJson('/api/payments/webhook/bkash', [
                'merchantInvoiceNumber' => $appointment->appointment_number,
                'amount' => 1200,
                'currency' => 'USD',
                'status' => 'COMPLETED',
            ])->assertStatus(422);

        // Underpayment (e.g. 500 instead of 1200) -> 422
        $this->withHeaders(['X-Webhook-Secret' => 'test-bkash-secret-key'])
            ->postJson('/api/payments/webhook/bkash', [
                'merchantInvoiceNumber' => $appointment->appointment_number,
                'amount' => 500,
                'currency' => 'BDT',
                'status' => 'COMPLETED',
            ])->assertStatus(422);

        // Valid bKash Payment -> 200 Confirmed
        $trxId = 'BKASH-TRX-'.Str::random(8);
        $this->withHeaders(['X-Webhook-Secret' => 'test-bkash-secret-key'])
            ->postJson('/api/payments/webhook/bkash', [
                'merchantInvoiceNumber' => $appointment->appointment_number,
                'amount' => 1200,
                'currency' => 'BDT',
                'trxID' => $trxId,
                'status' => 'COMPLETED',
            ])->assertStatus(200);

        $this->assertSame('paid', $appointment->fresh()->payment_status);
        $this->assertSame('confirmed', $appointment->fresh()->status);

        // Idempotency: Duplicate callback returns 200 without duplicate action
        $this->withHeaders(['X-Webhook-Secret' => 'test-bkash-secret-key'])
            ->postJson('/api/payments/webhook/bkash', [
                'merchantInvoiceNumber' => $appointment->appointment_number,
                'amount' => 1200,
                'currency' => 'BDT',
                'trxID' => $trxId,
                'status' => 'COMPLETED',
            ])->assertStatus(200)
            ->assertJsonFragment(['message' => 'Payment already completed previously.']);

        // --- Nagad Webhook Tests ---
        $aptNagad = $this->makeAppointment($doctor, $patient, 1500.00);
        config(['services.nagad.webhook_secret' => 'test-nagad-secret-key']);

        // Missing secret -> 403
        $this->postJson('/api/payments/webhook/nagad', [
            'order_id' => $aptNagad->appointment_number,
            'amount' => 1500,
            'status' => 'SUCCESS',
        ])->assertStatus(403);

        // Underpayment -> 422
        $this->withHeaders(['X-Webhook-Secret' => 'test-nagad-secret-key'])
            ->postJson('/api/payments/webhook/nagad', [
                'order_id' => $aptNagad->appointment_number,
                'amount' => 100,
                'currency' => 'BDT',
                'status' => 'SUCCESS',
            ])->assertStatus(422);

        // Currency mismatch -> 422
        $this->withHeaders(['X-Webhook-Secret' => 'test-nagad-secret-key'])
            ->postJson('/api/payments/webhook/nagad', [
                'order_id' => $aptNagad->appointment_number,
                'amount' => 1500,
                'currency' => 'EUR',
                'status' => 'SUCCESS',
            ])->assertStatus(422);

        // Replay attack: try to reuse the bKash TrxID on Nagad -> 409
        $this->withHeaders(['X-Webhook-Secret' => 'test-nagad-secret-key'])
            ->postJson('/api/payments/webhook/nagad', [
                'order_id' => $aptNagad->appointment_number,
                'payment_ref_id' => $trxId,
                'amount' => 1500,
                'currency' => 'BDT',
                'status' => 'SUCCESS',
            ])->assertStatus(409);

        // Valid Nagad Payment -> 200 Confirmed
        $nagadTrxId = 'NAGAD-TRX-'.Str::random(8);
        $this->withHeaders(['X-Webhook-Secret' => 'test-nagad-secret-key'])
            ->postJson('/api/payments/webhook/nagad', [
                'order_id' => $aptNagad->appointment_number,
                'payment_ref_id' => $nagadTrxId,
                'amount' => 1500,
                'currency' => 'BDT',
                'status' => 'SUCCESS',
            ])->assertStatus(200);

        $this->assertSame('paid', $aptNagad->fresh()->payment_status);

        // --- SSLCommerz IPN Tests ---
        $aptSsl = $this->makeAppointment($doctor, $patient, 2000.00);
        config(['services.sslcommerz.webhook_secret' => 'test-ssl-secret-key']);

        // Missing secret -> 403
        $this->postJson('/api/payments/webhook/sslcommerz', [
            'tran_id' => $aptSsl->appointment_number,
            'amount' => 2000,
            'status' => 'VALID',
        ])->assertStatus(403);

        // Underpayment -> 422
        $this->withHeaders(['X-Webhook-Secret' => 'test-ssl-secret-key'])
            ->postJson('/api/payments/webhook/sslcommerz', [
                'tran_id' => $aptSsl->appointment_number,
                'amount' => 50,
                'currency' => 'BDT',
                'status' => 'VALID',
            ])->assertStatus(422);

        // Replay attack: try to reuse nagadTrxId -> 409
        $this->withHeaders(['X-Webhook-Secret' => 'test-ssl-secret-key'])
            ->postJson('/api/payments/webhook/sslcommerz', [
                'tran_id' => $aptSsl->appointment_number,
                'val_id' => $nagadTrxId,
                'amount' => 2000,
                'currency' => 'BDT',
                'status' => 'VALID',
            ])->assertStatus(409);

        // Valid SSLCommerz IPN -> 200 Confirmed
        $sslValId = 'SSL-VAL-'.Str::random(8);
        $this->withHeaders(['X-Webhook-Secret' => 'test-ssl-secret-key'])
            ->postJson('/api/payments/webhook/sslcommerz', [
                'tran_id' => $aptSsl->appointment_number,
                'val_id' => $sslValId,
                'amount' => 2000,
                'currency' => 'BDT',
                'status' => 'VALID',
            ])->assertStatus(200);

        $this->assertSame('paid', $aptSsl->fresh()->payment_status);
    }

    /**
     * 4. SEC-09: Stored XSS Database Sanitization Command & Zero v-html verification
     */
    public function test_portfolio_clean_xss_command_and_frontend_template_safety(): void
    {
        $doctorUser = $this->makeUser('doctor');
        $xssData = [
            'hero' => [
                'title' => 'Cardiologist <script>alert("pwned")</script>',
                'stamp_title' => '<img src=x onerror=alert(1)>Specialist',
                'stamp_sub' => '15+ Years <iframe src="evil.com"></iframe>',
            ],
            'about' => [
                'bio' => 'Senior Surgeon at javascript:alert(document.cookie)',
            ],
        ];

        $doctor = $this->makeDoctor($doctorUser, 1000);
        $doctor->update(['portfolio_data' => $xssData]);

        // Dry-run mode: verify database record is NOT modified
        $this->artisan('portfolio:clean-xss', ['--dry-run' => true])
            ->assertExitCode(0);

        $this->assertEquals($xssData, $doctor->fresh()->portfolio_data);

        // Live mode: verify dangerous HTML & scripts are stripped
        $this->artisan('portfolio:clean-xss')
            ->assertExitCode(0);

        $cleaned = $doctor->fresh()->portfolio_data;
        $this->assertStringNotContainsString('<script>', json_encode($cleaned));
        $this->assertStringNotContainsString('<iframe', json_encode($cleaned));
        $this->assertStringNotContainsString('onerror=', json_encode($cleaned));
        $this->assertStringNotContainsString('javascript:', json_encode($cleaned));

        // Verify Vue templates have zero v-html
        $vueDir = resource_path('js');
        $vHtmlMatches = [];
        $files = File::allFiles($vueDir);
        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['vue', 'js'], true)) {
                $content = File::get($file->getPathname());
                if (str_contains($content, 'v-html')) {
                    $vHtmlMatches[] = $file->getFilename();
                }
            }
        }
        $this->assertEmpty($vHtmlMatches, 'Found forbidden v-html in Vue files: '.implode(', ', $vHtmlMatches));
    }
}
