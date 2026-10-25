<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadReportRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\PatientReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PatientReportController extends Controller
{
    /**
     * Get patient's uploaded medical & lab reports.
     */
    public function myReports(Request $request): JsonResponse
    {
        $authUser = $request->user();
        $userId = ($authUser && $authUser->role === 'admin' && $request->filled('user_id'))
            ? $request->input('user_id')
            : ($authUser ? $authUser->id : $request->input('user_id'));

        $query = PatientReport::where('patient_id', $userId)
            ->with(['doctor', 'appointment'])
            ->orderByDesc('created_at');

        if ($request->filled('test_type') && $request->input('test_type') !== 'all') {
            $query->where('test_type', $request->input('test_type'));
        }

        if ($request->filled('file_type') && $request->input('file_type') !== 'all') {
            $query->where('file_type', $request->input('file_type'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('report_title', 'like', "%{$s}%")
                    ->orWhere('report_number', 'like', "%{$s}%")
                    ->orWhere('diagnostic_center', 'like', "%{$s}%");
            });
        }

        $reports = $query->get();

        $stats = PatientReport::where('patient_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN file_type = 'image' THEN 1 END) as images,
                COUNT(CASE WHEN file_type = 'pdf' THEN 1 END) as pdfs,
                COUNT(CASE WHEN status = 'reviewed' THEN 1 END) as reviewed
            ")
            ->first();

        return response()->json([
            'success' => true,
            'reports' => $reports,
            'counts' => [
                'total' => (int) ($stats->total ?? 0),
                'images' => (int) ($stats->images ?? 0),
                'pdfs' => (int) ($stats->pdfs ?? 0),
                'reviewed' => (int) ($stats->reviewed ?? 0),
            ],
        ]);
    }

    /**
     * Upload a new medical report (Photo from camera or PDF).
     */
    public function upload(UploadReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        $userId = ($isAdmin && ! empty($validated['user_id']))
            ? $validated['user_id']
            : ($authUser ? $authUser->id : ($validated['user_id'] ?? null));

        $file = $request->file('report_file');

        // Determine file type and sanitize extension
        $mime = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (! in_array($mime, $allowedMimes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত ফাইল ফরম্যাট। শুধুমাত্র ছবি (JPG, PNG, WEBP) অথবা PDF গ্রহণযোগ্য।',
            ], 422);
        }

        $isPdf = str_contains($mime, 'pdf') || strtolower($file->getClientOriginalExtension()) === 'pdf';
        $fileType = $isPdf ? 'pdf' : 'image';

        // Format file size
        $bytes = $file->getSize();
        $fileSize = $this->formatFileSize($bytes);

        $safeExtension = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (! in_array($safeExtension, $allowedExtensions, true)) {
            $safeExtension = $isPdf ? 'pdf' : 'jpg';
        }

        $fileName = 'report_'.time().'_'.uniqid().'.'.$safeExtension;
        $storedPath = $file->storeAs('reports', $fileName, 'local');

        $report = PatientReport::create([
            'report_number' => PatientReport::generateReportNumber(),
            'patient_id' => $userId,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'report_title' => $validated['report_title'],
            'test_type' => ($validated['test_type'] ?? null) ?: 'blood_test',
            'diagnostic_center' => $validated['diagnostic_center'] ?? null,
            'report_date' => ($validated['report_date'] ?? null) ?: now()->format('Y-m-d'),
            'file_path' => $storedPath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'patient_notes' => $validated['patient_notes'] ?? $validated['notes'] ?? null,
            'status' => 'uploaded',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'রিপোর্টটি সফলভাবে আপলোড করা হয়েছে! (Medical report uploaded successfully)',
            'report' => $report->load(['doctor', 'appointment']),
        ], 201);
    }

    /**
     * Delete an uploaded report.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 401);
        }

        $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);
        $query = PatientReport::where('id', $id);

        // Non-admin can only delete their own report
        if (! $isAdmin) {
            $query->where('patient_id', $authUser->id);
        }

        $report = $query->firstOrFail();

        // Delete physical file if exists
        if (Storage::disk('local')->exists($report->file_path)) {
            Storage::disk('local')->delete($report->file_path);
        } elseif (File::exists(public_path($report->file_path))) {
            File::delete(public_path($report->file_path));
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'মেডিকেল রিপোর্টটি মুছে ফেলা হয়েছে। (Report deleted successfully)',
        ]);
    }

    /**
     * Securely stream or download an uploaded patient report.
     */
    public function download(int $id, Request $request)
    {
        $report = PatientReport::findOrFail($id);

        $isAuthorized = false;
        if ($request->hasValidSignature()) {
            $isAuthorized = true;
        } else {
            $user = $request->user('sanctum');
            if ($user) {
                if (in_array($user->role, ['admin', 'super_admin'], true) || $user->id === $report->patient_id) {
                    $isAuthorized = true;
                } elseif ($user->role === 'doctor') {
                    $doc = Doctor::where('user_id', $user->id)->first();
                    if ($doc && Appointment::where('doctor_id', $doc->id)->where('patient_id', $report->patient_id)->exists()) {
                        $isAuthorized = true;
                    }
                } elseif ($user instanceof DoctorAssistant) {
                    if (Appointment::where('doctor_id', $user->doctor_id)->where('patient_id', $report->patient_id)->exists()) {
                        $isAuthorized = true;
                    }
                }
            }
        }

        if (! $isAuthorized) {
            abort(403, 'অননুমোদিত অ্যাক্সেস! মেডিকেল রিপোর্ট দেখার অনুমতি নেই। (Unauthorized access to medical report)');
        }

        if (Storage::disk('local')->exists($report->file_path)) {
            $mime = Storage::disk('local')->mimeType($report->file_path) ?: ($report->file_type === 'pdf' ? 'application/pdf' : 'image/jpeg');
            $disposition = $request->query('download') ? 'attachment' : 'inline';

            return response()->file(Storage::disk('local')->path($report->file_path), [
                'Content-Type' => $mime,
                'Content-Disposition' => $disposition.'; filename="'.basename($report->file_path).'"',
            ]);
        }

        $publicPath = public_path($report->file_path);
        if (File::exists($publicPath)) {
            return response()->file($publicPath);
        }

        abort(404, 'রিপোর্ট ফাইলটি পাওয়া যায়নি। (Report file not found)');
    }

    /**
     * Doctor Portal: Get all reports for a specific patient.
     */
    public function doctorPatientReports(int $patientId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 401);
        }

        $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);

        // If not admin, ensure doctor or staff has an actual appointment relationship with this patient
        if (! $isAdmin) {
            $allowedDoctorId = null;
            if ($authUser instanceof DoctorAssistant) {
                $allowedDoctorId = $authUser->doctor_id;
            } elseif ($authUser->role === 'doctor') {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                $allowedDoctorId = $doc?->id;
            }

            if (! $allowedDoctorId) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
            }

            $hasRelationship = Appointment::where('doctor_id', $allowedDoctorId)
                ->where('patient_id', $patientId)
                ->exists();

            if (! $hasRelationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'অননুমোদিত অ্যাক্সেস! শুধুমাত্র চিকিৎসাধীন রোগীর রিপোর্ট দেখার অনুমতি রয়েছে।',
                ], 403);
            }
        }

        $reports = PatientReport::where('patient_id', $patientId)
            ->with(['doctor', 'appointment'])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'reports' => $reports,
        ]);
    }

    /**
     * Doctor Portal: Doctor adds remarks/feedback to a patient's report.
     */
    public function addDoctorRemarks(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'doctor_remarks' => 'required|string|max:1000',
            'doctor_id' => 'nullable|exists:doctors,id',
            'status' => 'nullable|in:uploaded,reviewed,urgent',
        ]);

        $report = PatientReport::findOrFail($id);
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);

        if (! $isAdmin) {
            $doctor = Doctor::where('user_id', $authUser?->id)->first();
            if (! $doctor) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
            }

            $hasRelationship = Appointment::where('doctor_id', $doctor->id)
                ->where('patient_id', $report->patient_id)
                ->exists();

            if (! $hasRelationship && $report->doctor_id !== $doctor->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'অননুমোদিত অ্যাক্সেস! আপনি এই রোগীর নির্ধারিত চিকিৎসক নন।',
                ], 403);
            }

            $report->doctor_id = $doctor->id;
        } elseif ($request->filled('doctor_id')) {
            $report->doctor_id = $request->input('doctor_id');
        }

        $report->doctor_remarks = $request->input('doctor_remarks');
        $report->status = $request->input('status', 'reviewed');
        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'ডাক্তারের মতামত রিপোর্টটিতে সংরক্ষণ করা হয়েছে।',
            'report' => $report->fresh(['doctor', 'patient']),
        ]);
    }

    /**
     * Format bytes to human readable string (KB / MB).
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
