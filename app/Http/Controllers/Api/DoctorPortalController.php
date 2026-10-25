<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DoctorPortalController extends Controller
{
    /**
     * Securely resolve the authorized doctor profile.
     */
    private function resolveDoctor(Request $request): Doctor
    {
        $user = $request->user();

        // 1. Staff / Assistant calling for their assigned doctor
        if ($user instanceof DoctorAssistant) {
            return $user->doctor ?: Doctor::findOrFail($user->doctor_id);
        }

        // 2. Super Admin or Admin inspecting a doctor
        if ($user && in_array($user->role, ['admin', 'super_admin'], true) && $request->filled('doctor_id')) {
            return Doctor::findOrFail($request->input('doctor_id'));
        }

        // 3. Authenticated Doctor user
        if ($user) {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($doctor) {
                return $doctor;
            }
        }

        // Super Admin fallback without specific doctor_id (first doctor)
        if ($user && in_array($user->role, ['admin', 'super_admin'], true)) {
            $firstDoc = Doctor::first();
            if ($firstDoc) {
                return $firstDoc;
            }
        }

        abort(403, 'ডাক্তারের প্রোফাইল শনাক্ত করা যায়নি বা আপনার এই পোর্টালে প্রবেশের অনুমতি নেই।');
    }

    /**
     * Get doctor's dashboard metrics & revenue.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        $doctorId = $doctor->id;
        $today = now()->format('Y-m-d');

        $totalEarnings = Payment::where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->sum('doctor_earning');

        $stats = [
            'total_appointments' => Appointment::where('doctor_id', $doctorId)->count(),
            'today_appointments' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', $today)->count(),
            'pending_appointments' => Appointment::where('doctor_id', $doctorId)->where('status', 'pending')->count(),
            'completed_appointments' => Appointment::where('doctor_id', $doctorId)->where('status', 'completed')->count(),
            'total_prescriptions' => Prescription::where('doctor_id', $doctorId)->count(),
            'total_earnings' => round($totalEarnings, 2),
        ];

        return response()->json([
            'doctor' => $doctor->load(['chambers' => function ($q) {
                $q->orderByDesc('is_primary')->orderBy('id');
            }]),
            'stats' => $stats,
        ]);
    }

    /**
     * Get appointments for the doctor.
     */
    public function appointments(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        $doctorId = $doctor->id;

        $query = Appointment::where('doctor_id', $doctorId)
            ->with(['patient', 'prescription'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('appointment_number', 'like', "%{$s}%")
                    ->orWhere('patient_name', 'like', "%{$s}%")
                    ->orWhere('patient_phone', 'like', "%{$s}%");
            });
        }

        $appointments = $query->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'doctor_notes' => 'nullable|string',
        ]);

        $doctor = $this->resolveDoctor($request);
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($id);
        $appointment->status = $request->input('status');
        if ($request->has('doctor_notes')) {
            $appointment->doctor_notes = $request->input('doctor_notes');
        }
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => "Appointment {$appointment->appointment_number} status updated to {$appointment->status}.",
            'appointment' => $appointment,
        ]);
    }

    /**
     * Doctor updates their own profile, schedule, and chamber address.
     */
    public function updateSchedule(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'qualifications' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:70',
            'chamber_address' => 'nullable|string|max:1000',
            'telemedicine_link' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'consultation_fee' => 'nullable|numeric|min:0',
            'telemedicine_fee' => 'nullable|numeric|min:0',
            'available_days' => 'nullable|array',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_duration_minutes' => 'nullable|integer|min:5|max:240',
            'free_consultation_enabled' => 'nullable|boolean',
            'free_days' => 'nullable|array',
            'free_start_time' => 'nullable|string',
            'free_end_time' => 'nullable|string',
            'free_patient_limit' => 'nullable|integer|min:1|max:500',
            'free_chamber_address' => 'nullable|string|max:1000',
            'free_service_note' => 'nullable|string|max:1000',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        $doctor->update($data);

        // Update linked user if name or password changed
        if ($doctor->user_id) {
            $user = User::find($doctor->user_id);
            if ($user) {
                if (! empty($data['name'])) {
                    $user->name = $data['name'];
                }
                if (! empty($data['phone'])) {
                    $user->phone = $data['phone'];
                }
                if (! empty($data['password'])) {
                    $user->password = bcrypt($data['password']);
                }
                $user->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your doctor profile, schedule, and chamber details have been updated successfully!',
            'doctor' => $doctor->fresh(),
        ]);
    }

    /**
     * Doctor updates live video consultation link for a specific appointment.
     */
    public function updateMeetingLink(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'meeting_link' => 'required|string|max:255',
        ]);

        $doctor = $this->resolveDoctor($request);
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($id);
        $appointment->meeting_link = $request->input('meeting_link');
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Video consultation meeting link updated!',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Doctor writes a digital prescription for a patient.
     */
    public function createPrescription(Request $request): JsonResponse
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_name' => 'required|string',
            'patient_age_gender' => 'nullable|string',
            'diagnosis' => 'required|string',
            'medicines' => 'required|array|min:1',
            'medical_tests' => 'nullable|array',
            'advice' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $doctor = $this->resolveDoctor($request);
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($data['appointment_id']);

        // Smartly enrich medicines with company, unit price & auto cost calculation
        $enrichedMedicines = $this->enrichMedicines($data['medicines']);

        // Check if prescription already exists for this appointment
        $prescription = Prescription::where('appointment_id', $appointment->id)->first();

        if ($prescription) {
            $prescription->update([
                'patient_name' => $data['patient_name'],
                'patient_age_gender' => $data['patient_age_gender'] ?? null,
                'diagnosis' => $data['diagnosis'],
                'medicines' => $enrichedMedicines,
                'medical_tests' => $data['medical_tests'] ?? [],
                'advice' => $data['advice'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null,
            ]);
        } else {
            $prescription = Prescription::create([
                'prescription_number' => Prescription::generatePrescriptionNumber(),
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'patient_name' => $data['patient_name'],
                'patient_age_gender' => $data['patient_age_gender'] ?? null,
                'diagnosis' => $data['diagnosis'],
                'medicines' => $enrichedMedicines,
                'medical_tests' => $data['medical_tests'] ?? [],
                'advice' => $data['advice'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null,
            ]);
        }

        // Auto mark appointment as completed
        $appointment->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'ডিজিটাল প্রেসক্রিপশন সফলভাবে প্রদান করা হয়েছে!',
            'prescription' => $prescription->load(['doctor', 'appointment']),
        ]);
    }

    /**
     * Smartly enrich medicines with company, unit price, quantity, and total cost behind the scenes (Zero Doctor Friction).
     */
    private function enrichMedicines(array $rawMedicines): array
    {
        $enriched = [];

        foreach ($rawMedicines as $m) {
            $rawName = trim($m['name'] ?? '');
            $dosage = $m['dosage'] ?? '1 + 0 + 1';
            $duration = $m['duration'] ?? '7 Days';
            $instruction = $m['instruction'] ?? 'খাবার পর';

            // Clean medicine name for search
            $cleanName = preg_replace('/^(Tab\.|Cap\.|Syrup\.|Inj\.|Susp\.|Drop\.)\s*/i', '', $rawName);
            $pureBrand = trim(preg_replace('/\s*\(.*?\)\s*/', '', $cleanName));
            $noSpaceBrand = preg_replace('/\s+/', '', $pureBrand);
            $firstWord = explode(' ', $pureBrand)[0];

            // Look up in Medicine table with spacing normalization and prioritizing non-zero prices
            $found = Medicine::where(function ($q) use ($pureBrand, $noSpaceBrand, $firstWord) {
                $q->where('brand_name', 'like', "%{$pureBrand}%")
                    ->orWhereRaw("REPLACE(brand_name, ' ', '') LIKE ?", ["%{$noSpaceBrand}%"])
                    ->orWhere('brand_name', 'like', "%{$firstWord}%");
            })
                ->orderByRaw('CASE WHEN unit_price > 0 THEN 0 ELSE 1 END')
                ->first();

            $company = ! empty($m['company']) ? $m['company'] : ($found?->company ?: null);
            if (! $company) {
                if (stripos($rawName, 'nebita') !== false) {
                    $company = 'Square Pharmaceuticals Ltd.';
                } elseif (stripos($rawName, 'lipicon') !== false) {
                    $company = 'Eskayef Pharmaceuticals Ltd.';
                } elseif (stripos($rawName, 'omega') !== false) {
                    $company = 'Square Pharmaceuticals Ltd.';
                } else {
                    $company = 'General BD Pharma';
                }
            }

            $unitPrice = ! empty($m['unit_price']) ? (float) $m['unit_price'] : (($found && $found->unit_price > 0) ? (float) $found->unit_price : null);
            if (! $unitPrice) {
                if (stripos($rawName, 'nebita') !== false) {
                    $unitPrice = 8.00;
                } elseif (stripos($rawName, 'lipicon') !== false) {
                    $unitPrice = 12.00;
                } elseif (stripos($rawName, 'omega') !== false) {
                    $unitPrice = 15.00;
                } else {
                    $unitPrice = 2.50;
                }
            }

            // Calculate daily quantity from dosage e.g. 1+0+1 = 2
            $dailyDose = 2;
            if (str_contains($dosage, '+')) {
                $parts = explode('+', $dosage);
                $dailyDose = 0;
                foreach ($parts as $p) {
                    $dailyDose += (float) trim($p);
                }
                if ($dailyDose <= 0) {
                    $dailyDose = 2;
                }
            } elseif (str_contains($dosage, '৩ বার')) {
                $dailyDose = 3;
            } elseif (str_contains($dosage, '২ বার')) {
                $dailyDose = 2;
            } elseif (str_contains($dosage, '১ বার')) {
                $dailyDose = 1;
            }

            // Calculate duration days (supports Bengali & English numbers)
            $cleanDuration = strtr($duration, [
                '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
                '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
            ]);
            $days = 7;
            if (preg_match('/(\d+)\s*(?:দিন|day|Days)/i', $cleanDuration, $dm)) {
                $days = (int) $dm[1];
            } elseif (preg_match('/(\d+)\s*(?:মাস|month|Months)/i', $cleanDuration, $mm)) {
                $days = (int) $mm[1] * 30;
            } elseif (preg_match('/(\d+)\s*(?:সপ্তাহ|week|Weeks)/i', $cleanDuration, $wm)) {
                $days = (int) $wm[1] * 7;
            }

            $totalQty = max(1, (int) round($dailyDose * $days));
            $itemTotal = round($totalQty * $unitPrice, 2);

            $enriched[] = [
                'name' => $rawName,
                'dosage' => $dosage,
                'duration' => $duration,
                'instruction' => $instruction,
                'company' => $company,
                'unit_price' => $unitPrice,
                'total_quantity' => $totalQty,
                'item_total' => $itemTotal,
            ];
        }

        return $enriched;
    }

    /**
     * Doctor requests / upgrades their SaaS Membership plan (Pro or VIP).
     */
    public function upgradeMembership(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $data = $request->validate([
            'plan' => 'required|in:pro,vip',
            'duration_months' => 'nullable|integer|in:1,6,12',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ]);

        $months = (int) ($data['duration_months'] ?? 1);
        $doctor->membership_plan = $data['plan'];
        $doctor->is_featured = true;
        $doctor->membership_expires_at = now()->addMonths($months);
        $doctor->save();

        Cache::forget("doctor_profile_{$doctor->slug}");
        Cache::forget("doctor_profile_{$doctor->id}");

        return response()->json([
            'success' => true,
            'message' => 'অভিনন্দন! আপনার প্রোফাইলটি সফলভাবে '.strtoupper($data['plan']).' প্ল্যানে আপগ্রেড করা হয়েছে। এখন থেকে আপনি টপ সার্চ র‍্যাংকিং ও গোল্ডেন প্রো ব্যাজ উপভোগ করবেন।',
            'doctor' => $doctor->fresh(),
        ]);
    }

    /**
     * Get doctor portfolio configuration and contents for dashboard editor.
     */
    public function getPortfolio(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        $doctor->load('chambers');

        return response()->json([
            'success' => true,
            'doctor' => $doctor,
            'portfolio_data' => $doctor->portfolio_settings,
        ]);
    }

    /**
     * Update doctor portfolio configuration (Hero, About, Education, Tips, Services, Reels, FAQs, Testimonials, Gallery, Contact).
     */
    public function updatePortfolio(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $payload = $request->input('portfolio_data', []);
        if (is_string($payload)) {
            $payload = json_decode($payload, true) ?: [];
        }

        $sanitizeInput = function ($val) use (&$sanitizeInput) {
            if (is_array($val)) {
                return array_map($sanitizeInput, $val);
            }
            if (is_string($val)) {
                return strip_tags($val);
            }

            return $val;
        };

        $payload = $sanitizeInput($payload);

        // Merge or replace
        $current = $doctor->portfolio_data ?? [];
        $merged = array_replace_recursive($current, $payload);

        // Allow direct arrays replacement for list items so deleted items aren't kept by array_replace_recursive
        $listKeys = ['short_videos', 'services', 'education', 'tips', 'testimonials', 'faqs', 'gallery'];
        foreach ($listKeys as $k) {
            if ($request->has("portfolio_data.{$k}")) {
                $merged[$k] = $sanitizeInput($request->input("portfolio_data.{$k}"));
            } elseif (isset($payload[$k]) && is_array($payload[$k])) {
                $merged[$k] = $payload[$k];
            }
        }

        if (isset($payload['template'])) {
            $merged['template'] = $payload['template'];
        } elseif ($request->has('portfolio_data.template')) {
            $merged['template'] = strip_tags($request->input('portfolio_data.template'));
        }

        // Sync root doctor fields if passed
        if ($request->filled('bio')) {
            $doctor->bio = strip_tags($request->input('bio'));
        }
        if ($request->has('free_consultation_enabled')) {
            $doctor->free_consultation_enabled = (bool) $request->input('free_consultation_enabled');
            $rawFreeDays = $request->input('free_days');
            if (is_array($rawFreeDays)) {
                $doctor->free_days = array_values(array_filter(array_map(fn ($d) => strip_tags((string) $d), $rawFreeDays)));
            } elseif (is_string($rawFreeDays)) {
                $doctor->free_days = array_values(array_filter(array_map('trim', explode(',', strip_tags($rawFreeDays)))));
            }
            $doctor->free_start_time = strip_tags((string) $request->input('free_start_time', $doctor->free_start_time));
            $doctor->free_end_time = strip_tags((string) $request->input('free_end_time', $doctor->free_end_time));
            $doctor->free_chamber_address = strip_tags((string) $request->input('free_chamber_address', $doctor->free_chamber_address));
            $doctor->free_service_note = strip_tags((string) $request->input('free_service_note', $doctor->free_service_note));
        }

        $doctor->portfolio_data = $sanitizeInput($merged);
        $doctor->save();

        Cache::forget("doctor_profile_{$doctor->slug}");
        Cache::forget("doctor_profile_{$doctor->id}");

        return response()->json([
            'success' => true,
            'message' => 'পোর্টফোলিও সফলভাবে আপডেট ও সংরক্ষণ করা হয়েছে!',
            'doctor' => $doctor->fresh()->load('chambers'),
            'portfolio_data' => $doctor->fresh()->portfolio_settings,
        ]);
    }

    /**
     * Upload photo for portfolio gallery or avatar.
     */
    public function uploadPortfolioMedia(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $request->validate([
            'media' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        $file = $request->file('media') ?: $request->file('file') ?: $request->file('image');
        if (! $file || ! $file->isValid()) {
            return response()->json(['success' => false, 'message' => 'একটি সঠিক ছবি আপলোড করুন।'], 422);
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($file->getMimeType(), $allowedMimes, true)) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত ছবির ফরম্যাট। (Invalid image MIME type)'], 422);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        if (! in_array($ext, $allowedExts, true)) {
            $ext = 'jpg';
        }

        $fileName = 'portfolio_'.$doctor->id.'_'.time().'_'.uniqid().'.'.$ext;
        $targetDir = public_path('uploads/portfolio/media');
        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $file->move($targetDir, $fileName);

        $url = '/uploads/portfolio/media/'.$fileName;

        return response()->json([
            'success' => true,
            'message' => 'ছবি সফলভাবে আপলোড হয়েছে!',
            'url' => $url,
            'media_url' => $url,
        ]);
    }

    /**
     * Upload short video / reel file.
     */
    public function uploadPortfolioVideo(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $request->validate([
            'video' => 'nullable|file|mimes:mp4,mov,webm,mkv|max:51200',
            'file' => 'nullable|file|mimes:mp4,mov,webm,mkv|max:51200',
        ]);

        $file = $request->file('video') ?: $request->file('file');
        if (! $file || ! $file->isValid()) {
            return response()->json(['success' => false, 'message' => 'একটি সঠিক ভিডিও ফাইল আপলোড করুন।'], 422);
        }

        $allowedVideoMimes = ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-matroska'];
        if (! in_array($file->getMimeType(), $allowedVideoMimes, true)) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত ভিডিও ফরম্যাট। (Invalid video MIME type)'], 422);
        }

        $allowedVideoExts = ['mp4', 'mov', 'webm', 'mkv'];
        $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        if (! in_array($ext, $allowedVideoExts, true)) {
            $ext = 'mp4';
        }

        $fileName = 'reel_'.$doctor->id.'_'.time().'_'.uniqid().'.'.$ext;
        $targetDir = public_path('uploads/portfolio/videos');
        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $file->move($targetDir, $fileName);

        $url = '/uploads/portfolio/videos/'.$fileName;

        return response()->json([
            'success' => true,
            'message' => 'ভিডিও সফলভাবে আপলোড হয়েছে!',
            'url' => $url,
            'video_url' => $url,
        ]);
    }
}
