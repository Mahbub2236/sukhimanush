<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\HealthMetric;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthMetricController extends Controller
{
    /**
     * Resolve effective user ID considering role and authorization.
     */
    protected function resolveUserId(Request $request): int
    {
        $authUser = $request->user();
        if ($authUser && in_array($authUser->role, ['admin', 'super_admin', 'doctor'], true) && $request->filled('user_id')) {
            return (int) $request->input('user_id');
        }
        if ($authUser) {
            return (int) $authUser->id;
        }

        return (int) $request->input('user_id');
    }

    /**
     * Get chronic health readings and chart trends (Diabetes, BP, Weight).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $metricType = $request->input('metric_type', 'blood_sugar');
        $familyMemberId = $request->input('family_member_id');

        $query = HealthMetric::where('user_id', $userId)
            ->where('metric_type', $metricType)
            ->orderBy('recorded_date');

        if ($familyMemberId) {
            $query->where('family_member_id', $familyMemberId);
        }

        $metrics = $query->get();

        // Calculate summary stats
        $avgPrimary = $metrics->avg('value_primary');
        $maxPrimary = $metrics->max('value_primary');
        $minPrimary = $metrics->min('value_primary');
        $latest = $metrics->last();

        return response()->json([
            'success' => true,
            'metric_type' => $metricType,
            'metrics' => $metrics,
            'stats' => [
                'count' => $metrics->count(),
                'average' => $avgPrimary ? round($avgPrimary, 1) : null,
                'max' => $maxPrimary ? round($maxPrimary, 1) : null,
                'min' => $minPrimary ? round($minPrimary, 1) : null,
                'latest' => $latest,
            ],
        ]);
    }

    /**
     * Log a new health reading (e.g. Sugar reading or BP).
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $data = $request->validate([
            'family_member_id' => 'nullable|exists:family_members,id',
            'metric_type' => 'required|in:blood_sugar,blood_pressure,weight,pulse,temperature',
            'value_primary' => 'required|numeric',
            'value_secondary' => 'nullable|numeric',
            'measured_time' => 'nullable|string|max:50',
            'recorded_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['user_id'] = $userId;
        $data['recorded_date'] = $data['recorded_date'] ?: now()->format('Y-m-d');

        $metric = HealthMetric::create($data);

        return response()->json([
            'success' => true,
            'message' => 'স্বাস্থ্য মাপের তথ্য সফলভাবে সেভ হয়েছে!',
            'metric' => $metric,
        ], 201);
    }

    /**
     * Delete a reading.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $metric = HealthMetric::findOrFail($id);

        $isAdmin = $request->user() && in_array($request->user()->role, ['admin', 'super_admin'], true);
        if ($request->user() && ! $isAdmin && $metric->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $metric->delete();

        return response()->json([
            'success' => true,
            'message' => 'রিডিংটি মুছে ফেলা হয়েছে।',
        ]);
    }

    /**
     * Doctor Portal: Get chronic health history of a patient.
     */
    public function doctorPatientMetrics(int $patientId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);
        if (! $isAdmin) {
            $allowedDoctorId = null;
            if ($authUser instanceof DoctorAssistant) {
                $allowedDoctorId = $authUser->doctor_id;
            } elseif ($authUser->role === 'doctor') {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                $allowedDoctorId = $doc?->id;
            }

            if (! $allowedDoctorId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $hasRelationship = Appointment::where('doctor_id', $allowedDoctorId)
                ->where('patient_id', $patientId)
                ->exists();

            if (! $hasRelationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'অননুমোদিত অ্যাক্সেস! শুধুমাত্র চিকিৎসাধীন রোগীর স্বাস্থ্য তথ্য দেখার অনুমতি রয়েছে।',
                ], 403);
            }
        }

        $sugars = HealthMetric::where('user_id', $patientId)
            ->where('metric_type', 'blood_sugar')
            ->orderBy('recorded_date')
            ->get();

        $bps = HealthMetric::where('user_id', $patientId)
            ->where('metric_type', 'blood_pressure')
            ->orderBy('recorded_date')
            ->get();

        return response()->json([
            'success' => true,
            'blood_sugar' => $sugars,
            'blood_pressure' => $bps,
        ]);
    }
}
