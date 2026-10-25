<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\DashboardController as SuperAdminController;
use App\Http\Controllers\Api\Admin\EmergencyResourceAdminController;
use App\Http\Controllers\Api\AgentPortalController;
use App\Http\Controllers\Api\AiHealthController;
use App\Http\Controllers\Api\AppointmentBookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BloodSosController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CostEstimatorController;
use App\Http\Controllers\Api\DiagnosticCenterController;
use App\Http\Controllers\Api\DiagnosticOrderController;
use App\Http\Controllers\Api\DoctorAssistantController;
use App\Http\Controllers\Api\DoctorChamberController;
use App\Http\Controllers\Api\DoctorPortalController;
use App\Http\Controllers\Api\EmergencyController;
use App\Http\Controllers\Api\FamilyProfileController;
use App\Http\Controllers\Api\HealthMetricController;
use App\Http\Controllers\Api\HealthPassportController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LegalPolicyController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\MedicineReminderController;
use App\Http\Controllers\Api\PatientPortalController;
use App\Http\Controllers\Api\PatientReportController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PrescriptionAudioController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicDoctorController;
use App\Http\Controllers\Api\QueueTrackerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Doctor SaaS Multi-Tenant Platform API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Public Endpoints with High-Concurrency Throttling & Shield
Route::middleware(['throttle:300,1'])->group(function () {
    Route::get('/home-data', [HomeController::class, 'index']);
    Route::get('/doctors', [PublicDoctorController::class, 'index']);
    Route::get('/doctors/locations', [PublicDoctorController::class, 'locations']);
    Route::get('/doctors/{slug}', [PublicDoctorController::class, 'show']);
    Route::get('/doctors/{id}/slots', [PublicDoctorController::class, 'slots']);
    Route::get('/image-proxy', function (Request $request) {
        $url = $request->input('url');
        if (! $url || ! is_string($url)) {
            return response('No URL provided', 400);
        }

        // Validate that URL is strictly HTTP or HTTPS
        $parsed = parse_url($url);
        if (! isset($parsed['scheme'], $parsed['host']) || ! in_array(strtolower($parsed['scheme']), ['http', 'https'], true)) {
            return response('Invalid URL Scheme', 400);
        }

        $host = strtolower($parsed['host']);

        // Strict Domain Whitelist for trusted external medical & doctor assets
        $allowedDomains = [
            'images.unsplash.com',
            'raw.githubusercontent.com',
            'cdn.pixabay.com',
            'images.pexels.com',
            'avatars.githubusercontent.com',
            'images.freeimages.com',
        ];

        $isWhitelisted = false;
        foreach ($allowedDomains as $domain) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                $isWhitelisted = true;
                break;
            }
        }

        if (! $isWhitelisted) {
            return response('Forbidden: Host not in allowed whitelist', 403);
        }

        // Prevent SSRF to localhost or private network ranges
        $ip = gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return response('Restricted Destination IP', 403);
        }

        try {
            // Disable redirects, set 5s timeout, enforce image MIME and max 5MB size
            $response = Http::withoutRedirecting()
                ->timeout(5)
                ->get($url);

            if (! $response->successful()) {
                return response('Failed to fetch remote asset', $response->status() >= 400 && $response->status() < 500 ? $response->status() : 404);
            }

            $contentType = strtolower(explode(';', $response->header('Content-Type') ?: '')[0]);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (! in_array($contentType, $allowedMimes, true)) {
                return response('Invalid MIME type: only images allowed', 415);
            }

            $body = $response->body();
            if (strlen($body) > 5242880) { // 5MB limit
                return response('Image payload too large (max 5MB)', 413);
            }

            return response($body)
                ->header('Content-Type', $contentType)
                ->header('Content-Length', (string) strlen($body))
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('X-Content-Type-Options', 'nosniff');
        } catch (Throwable $e) {
            return response('Error fetching remote asset', 500);
        }
    });
});

// Sukhi Manush AI Health Assistant & Symptom Checker & Prescription Explainer
Route::middleware(['throttle:15,1'])->group(function () {
    Route::post('/ai/analyze-symptoms', [AiHealthController::class, 'analyzeSymptoms']);
    Route::get('/ai/quick-symptoms', [AiHealthController::class, 'quickSymptoms']);
    Route::get('/ai/tts', [AiHealthController::class, 'textToSpeech']);
    Route::post('/ai/explain-prescription', [AiHealthController::class, 'explainPrescription']);
    Route::get('/ai/sample-prescriptions', [AiHealthController::class, 'samplePrescriptions']);
});

// Medicine Autocomplete & Presets API
Route::get('/medicines/search', [MedicineController::class, 'search']);
Route::get('/medicines/presets', [MedicineController::class, 'presets']);

// Appointment Booking & Tracking (Protected with Anti-Spam Rate Limiter)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/appointments', [AppointmentBookingController::class, 'store']);
    Route::get('/appointments/track', [AppointmentBookingController::class, 'track']);
    Route::get('/appointments/video-chamber/{appointmentNumber}', [AppointmentBookingController::class, 'videoChamber']);
    Route::get('/queue/token/{appointmentId}', [QueueTrackerController::class, 'getPatientTokenStatus']);
    Route::get('/queue/status/{doctorId}', [QueueTrackerController::class, 'getQueueStatus']);
    Route::get('/patient-reports/stream/{id}', [PatientReportController::class, 'download'])->name('patient.reports.download');
});

// Contact & Blog
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);

// Public Legal Policies (Privacy Policy & Terms of Service - Bangladesh Health Compliant)
Route::get('/policies', [LegalPolicyController::class, 'index']);
Route::get('/policies/{key}', [LegalPolicyController::class, 'show']);

// Diagnostic Partner Centers & Referral Booking
Route::get('/diagnostic-centers', [DiagnosticCenterController::class, 'index']);
Route::get('/diagnostic-centers/{id}', [DiagnosticCenterController::class, 'show']);
Route::post('/diagnostic-centers/register', [DiagnosticCenterController::class, 'register']);
Route::post('/diagnostic-orders/book', [DiagnosticOrderController::class, 'store']);

// Studio Quality Human Voice Prescription Audio Stream
Route::get('/prescriptions/{id}/audio', [PrescriptionAudioController::class, 'getAudio']);

// Public Authentication Endpoints (Protected against Brute-Force & Credential Stuffing)
Route::middleware(['throttle:15,1'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/staff-login', [DoctorAssistantController::class, 'login']);
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
});

// Emergency Blood & Platelet SOS
Route::prefix('blood-sos')->group(function () {
    Route::get('/requests', [BloodSosController::class, 'requests']);
    Route::post('/requests', [BloodSosController::class, 'storeRequest']);
    Route::get('/donors', [BloodSosController::class, 'donors']);
    Route::post('/donors/register', [BloodSosController::class, 'registerDonor']);
    Route::post('/requests/{id}/respond', [BloodSosController::class, 'respondToSos']);
});

// 1-Tap Emergency Hub & Resources (Ambulance, ICU Beds)
Route::prefix('emergency')->group(function () {
    Route::get('/overview', [EmergencyController::class, 'overview']);
    Route::get('/ambulances', [EmergencyController::class, 'ambulances']);
    Route::get('/hospital-beds', [EmergencyController::class, 'hospitalBeds']);
});

// Medical Procedure Cost Estimator
Route::prefix('cost-estimator')->group(function () {
    Route::get('/procedures', [CostEstimatorController::class, 'index']);
    Route::get('/procedures/{id}', [CostEstimatorController::class, 'show']);
});

// Digital Health Passport Public QR Emergency Card View (Responders scan without login)
Route::get('/health-passport/emergency/{token}', [HealthPassportController::class, 'viewEmergencyPass']);

// Automated Payment Webhooks & IPN Listeners (bKash, Nagad, SSLCommerz)
Route::prefix('payments/webhook')->group(function () {
    Route::post('/bkash', [PaymentWebhookController::class, 'handleBkashWebhook']);
    Route::post('/nagad', [PaymentWebhookController::class, 'handleNagadWebhook']);
    Route::post('/sslcommerz', [PaymentWebhookController::class, 'handleSslcommerzIpn']);
});

/*
|--------------------------------------------------------------------------
| 2. PROTECTED ROUTES (Requires Valid Laravel Sanctum Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Authenticated User Details & Safe Logout
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profile & Avatar Management
    Route::post('/profile/upload-avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);

    // Admin & Staff Manual TrxID Payment Verification (Protected)
    Route::post('/payments/manual-verify', [PaymentWebhookController::class, 'verifyManualTrxId'])->middleware('role:admin,staff');
    Route::post('/payments/webhook/manual-verify', [PaymentWebhookController::class, 'verifyManualTrxId'])->middleware('role:admin,staff');

    // Staff / Compounder Chamber Portal API
    Route::prefix('staff-portal')->middleware('role:staff,doctor')->group(function () {
        Route::get('/dashboard/{assistantId}', [DoctorAssistantController::class, 'dashboard']);
        Route::post('/queue/call-next/{assistantId}', [DoctorAssistantController::class, 'callNext']);
        Route::patch('/appointments/{id}/status', [DoctorAssistantController::class, 'updateStatus']);
        Route::post('/walkin/{assistantId}', [DoctorAssistantController::class, 'createWalkin']);
    });

    // Doctor Portal API
    Route::prefix('doctor-portal')->middleware('role:doctor,staff')->group(function () {
        Route::get('/dashboard', [DoctorPortalController::class, 'dashboard']);
        Route::get('/appointments', [DoctorPortalController::class, 'appointments']);
        Route::patch('/appointments/{id}/status', [DoctorPortalController::class, 'updateStatus']);
        Route::patch('/appointments/{id}/meeting-link', [DoctorPortalController::class, 'updateMeetingLink']);
        Route::post('/schedule/update', [DoctorPortalController::class, 'updateSchedule']);
        Route::post('/prescriptions', [DoctorPortalController::class, 'createPrescription']);

        // Patient Reports & Remarks
        Route::get('/patient-reports/{patientId}', [PatientReportController::class, 'doctorPatientReports']);
        Route::post('/reports/{id}/remarks', [PatientReportController::class, 'addDoctorRemarks']);
        Route::post('/reports/upload', [PatientReportController::class, 'upload']);

        // Chronic Health Metrics & Patient BP/Diabetes History
        Route::get('/patient-metrics/{patientId}', [HealthMetricController::class, 'doctorPatientMetrics']);

        // Live Chamber Queue Controller
        Route::get('/queue/status/{doctorId}', [QueueTrackerController::class, 'getQueueStatus']);
        Route::post('/queue/call-next/{doctorId}', [QueueTrackerController::class, 'callNextPatient']);
        Route::post('/queue/emergency-bypass/{doctorId}', [QueueTrackerController::class, 'emergencyBypass']);

        // Multiple Chambers Management
        Route::get('/chambers', [DoctorChamberController::class, 'index']);
        Route::post('/chambers', [DoctorChamberController::class, 'store']);
        Route::put('/chambers/{id}', [DoctorChamberController::class, 'update']);
        Route::delete('/chambers/{id}', [DoctorChamberController::class, 'destroy']);
        Route::post('/chambers/{id}/set-primary', [DoctorChamberController::class, 'setPrimary']);

        // Doctor Assistants / Compounders
        Route::get('/assistants/{doctorId}', [DoctorAssistantController::class, 'index']);
        Route::post('/assistants/{doctorId}', [DoctorAssistantController::class, 'store']);
        Route::delete('/assistants/{id}', [DoctorAssistantController::class, 'destroy']);

        // Doctor Dynamic Portfolio & Short Videos Management
        Route::get('/portfolio', [DoctorPortalController::class, 'getPortfolio']);
        Route::post('/portfolio', [DoctorPortalController::class, 'updatePortfolio']);
        Route::post('/portfolio/upload-media', [DoctorPortalController::class, 'uploadPortfolioMedia']);
        Route::post('/portfolio/upload-video', [DoctorPortalController::class, 'uploadPortfolioVideo']);

        // Doctor SaaS Pro / VIP Membership Upgrade
        Route::post('/membership/upgrade', [DoctorPortalController::class, 'upgradeMembership']);
    });

    // Patient Portal API
    Route::prefix('patient-portal')->middleware('role:patient')->group(function () {
        Route::get('/dashboard', [PatientPortalController::class, 'dashboard']);
        Route::get('/my-doctors', [PatientPortalController::class, 'myDoctors']);
        Route::get('/my-appointments', [PatientPortalController::class, 'myAppointments']);
        Route::get('/my-prescriptions', [PatientPortalController::class, 'myPrescriptions']);
        Route::get('/my-reports', [PatientReportController::class, 'myReports']);
        Route::post('/reports/upload', [PatientReportController::class, 'upload']);
        Route::delete('/reports/{id}', [PatientReportController::class, 'destroy']);

        // Family Health Profiles
        Route::get('/family-members', [FamilyProfileController::class, 'index']);
        Route::post('/family-members', [FamilyProfileController::class, 'store']);
        Route::put('/family-members/{id}', [FamilyProfileController::class, 'update']);
        Route::delete('/family-members/{id}', [FamilyProfileController::class, 'destroy']);

        // Chronic Disease & Vital Health Metrics (Diabetes, BP, Weight)
        Route::get('/health-metrics', [HealthMetricController::class, 'index']);
        Route::post('/health-metrics', [HealthMetricController::class, 'store']);
        Route::delete('/health-metrics/{id}', [HealthMetricController::class, 'destroy']);

        // Live Queue Token Status
        Route::get('/queue/token/{appointmentId}', [QueueTrackerController::class, 'getPatientTokenStatus']);

        // Diagnostic Home Sample Orders
        Route::get('/diagnostic-orders', [DiagnosticOrderController::class, 'index']);
        Route::post('/diagnostic-orders', [DiagnosticOrderController::class, 'store']);
    });

    // Super Admin Portal API
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/stats', [SuperAdminController::class, 'stats']);
        Route::get('/appointments', [SuperAdminController::class, 'appointments']);
        Route::patch('/appointments/{id}/status', [SuperAdminController::class, 'updateAppointmentStatus']);
        Route::get('/doctors', [SuperAdminController::class, 'doctors']);
        Route::get('/doctors/{id}', [SuperAdminController::class, 'showDoctor']);
        Route::put('/doctors/{id}', [SuperAdminController::class, 'updateDoctor']);
        Route::delete('/doctors/{id}', [SuperAdminController::class, 'destroyDoctor']);
        Route::patch('/doctors/{id}/status', [SuperAdminController::class, 'toggleDoctorStatus']);
        Route::get('/payments', [SuperAdminController::class, 'payments']);
        Route::get('/messages', [SuperAdminController::class, 'messages']);
        Route::post('/messages/{id}/read', [SuperAdminController::class, 'markMessageRead']);

        // Agents (Rural Digital Health Points) Management
        Route::get('/agents', [SuperAdminController::class, 'agents']);
        Route::post('/agents', [SuperAdminController::class, 'storeAgent']);
        Route::patch('/agents/{id}/wallet', [SuperAdminController::class, 'updateAgentWallet']);
        Route::patch('/agents/{id}/status', [SuperAdminController::class, 'toggleAgentStatus']);
        Route::delete('/agents/{id}', [SuperAdminController::class, 'destroyAgent']);

        // Staff / Chamber Assistants Management
        Route::get('/staff', [SuperAdminController::class, 'staffList']);
        Route::post('/staff', [SuperAdminController::class, 'storeStaff']);
        Route::patch('/staff/{id}/toggle', [SuperAdminController::class, 'toggleStaffStatus']);
        Route::patch('/staff/{id}/pin', [SuperAdminController::class, 'resetStaffPin']);
        Route::delete('/staff/{id}', [SuperAdminController::class, 'destroyStaff']);

        // Patients Management
        Route::get('/patients', [SuperAdminController::class, 'patients']);
        Route::delete('/patients/{id}', [SuperAdminController::class, 'destroyPatient']);

        // Emergency Resources Management (Ambulance, ICU Beds, Blood SOS, Health Passports)
        Route::get('/ambulances', [EmergencyResourceAdminController::class, 'ambulances']);
        Route::post('/ambulances', [EmergencyResourceAdminController::class, 'storeAmbulance']);
        Route::put('/ambulances/{id}', [EmergencyResourceAdminController::class, 'updateAmbulance']);
        Route::delete('/ambulances/{id}', [EmergencyResourceAdminController::class, 'destroyAmbulance']);
        Route::patch('/ambulances/{id}/toggle', [EmergencyResourceAdminController::class, 'toggleAmbulanceStatus']);

        Route::get('/hospital-beds', [EmergencyResourceAdminController::class, 'hospitalBeds']);
        Route::post('/hospital-beds', [EmergencyResourceAdminController::class, 'storeHospitalBed']);
        Route::put('/hospital-beds/{id}', [EmergencyResourceAdminController::class, 'updateHospitalBed']);
        Route::delete('/hospital-beds/{id}', [EmergencyResourceAdminController::class, 'destroyHospitalBed']);
        Route::patch('/hospital-beds/{id}/verify', [EmergencyResourceAdminController::class, 'quickVerifyHospitalBed']);

        Route::get('/blood-requests', [EmergencyResourceAdminController::class, 'bloodRequests']);
        Route::patch('/blood-requests/{id}/status', [EmergencyResourceAdminController::class, 'updateBloodRequestStatus']);
        Route::delete('/blood-requests/{id}', [EmergencyResourceAdminController::class, 'destroyBloodRequest']);
        Route::get('/blood-donors', [EmergencyResourceAdminController::class, 'bloodDonors']);
        Route::patch('/blood-donors/{id}/toggle', [EmergencyResourceAdminController::class, 'toggleDonorAvailability']);
        Route::delete('/blood-donors/{id}', [EmergencyResourceAdminController::class, 'destroyBloodDonor']);

        Route::get('/health-passports', [EmergencyResourceAdminController::class, 'healthPassports']);
        Route::post('/health-passports/{userId}/regenerate', [EmergencyResourceAdminController::class, 'regeneratePassportToken']);

        // Diagnostic Orders & Partner Centers Management
        Route::get('/diagnostic-orders', [DiagnosticOrderController::class, 'index']);
        Route::patch('/diagnostic-orders/{id}/status', [DiagnosticOrderController::class, 'updateStatus']);
        Route::get('/diagnostic-centers', [DiagnosticCenterController::class, 'index']);
        Route::put('/diagnostic-centers/{id}', [DiagnosticCenterController::class, 'adminUpdate']);
        Route::patch('/doctors/{id}/membership', [SuperAdminController::class, 'updateDoctorMembership']);

        // Legal Policies Management (Privacy Policy, Terms of Service, Dynamic Updates)
        Route::get('/policies', [LegalPolicyController::class, 'adminIndex']);
        Route::put('/policies/{key}', [LegalPolicyController::class, 'adminUpdate']);
        Route::post('/policies/{key}/reset-default', [LegalPolicyController::class, 'adminResetDefault']);
    });

    // Live Chamber Queue & Assistant Controls
    Route::prefix('queue')->middleware('role:doctor,staff')->group(function () {
        Route::post('/call-next/{doctorId}', [QueueTrackerController::class, 'callNextPatient']);
        Route::post('/skip/{doctorId}/{appointmentId}', [QueueTrackerController::class, 'skipPatient']);
        Route::post('/doctor-status/{doctorId}', [QueueTrackerController::class, 'updateDoctorStatus']);
    });

    // Rural Digital Health Point / Pharmacy Agent Portal
    Route::prefix('agent-portal')->middleware('role:agent')->group(function () {
        Route::get('/dashboard/{agentId}', [AgentPortalController::class, 'dashboard']);
        Route::get('/patients/search', [AgentPortalController::class, 'searchPatients']);
        Route::post('/bookings/{agentId}', [AgentPortalController::class, 'createBooking']);
    });

    // Medicine Reminders & AI Prescription Extraction
    Route::prefix('medicines')->group(function () {
        Route::get('/schedules', [MedicineReminderController::class, 'index']);
        Route::post('/schedules', [MedicineReminderController::class, 'store']);
        Route::patch('/schedules/{id}/adherence', [MedicineReminderController::class, 'logAdherence']);
        Route::delete('/schedules/{id}', [MedicineReminderController::class, 'destroy']);
        Route::post('/ai-extract', [MedicineReminderController::class, 'aiExtractPrescription']);
    });

    // Digital Health Passport (User Private Endpoints)
    Route::prefix('health-passport')->group(function () {
        Route::get('/token/{userId}', [HealthPassportController::class, 'getOrCreateToken']);
        Route::post('/emergency-info/{userId}', [HealthPassportController::class, 'updateEmergencyInfo']);
    });
});
