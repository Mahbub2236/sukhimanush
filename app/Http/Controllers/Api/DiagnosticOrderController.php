<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticCenter;
use App\Models\DiagnosticOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticOrderController extends Controller
{
    /**
     * Get list of diagnostic orders for patient or admin.
     */
    public function index(Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 401);
        }

        $query = DiagnosticOrder::with(['doctor', 'prescription', 'patient', 'diagnosticCenter'])
            ->orderByDesc('created_at');

        $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);

        if (! $isAdmin) {
            $query->where('patient_id', $authUser->id);
        } elseif ($request->filled('user_id')) {
            $query->where('patient_id', $request->input('user_id'));
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Book a diagnostic test / Home sample collection with dynamic center commission.
     */
    public function store(Request $request): JsonResponse
    {
        $authUser = $request->user();

        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'patient_name' => 'nullable|string|max:150',
            'doctor_id' => 'nullable|exists:doctors,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'diagnostic_center_id' => 'nullable|exists:diagnostic_centers,id',
            'diagnostic_center' => 'nullable|string|max:255',
            'test_names' => 'required|array|min:1',
            'total_amount' => 'required|numeric|min:50',
            'sample_collection_type' => 'required|in:home_collection,lab_visit',
            'collection_address' => 'nullable|string|max:500',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string',
            'contact_phone' => 'required|string|min:7|max:20',
        ]);

        // Resolve patient ID
        $patientId = null;
        if ($authUser) {
            $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);
            $patientId = ($isAdmin && ! empty($data['user_id'])) ? (int) $data['user_id'] : (int) $authUser->id;
        } else {
            // Find or associate patient by phone/name
            $existingUser = User::where('phone', $data['contact_phone'])->first();
            $patientId = $existingUser ? $existingUser->id : ((int) (User::where('role', 'patient')->first()?->id ?: 1));
        }

        // Resolve Diagnostic Center & Calculate Dynamic Commission
        $center = null;
        if (! empty($data['diagnostic_center_id'])) {
            $center = DiagnosticCenter::find($data['diagnostic_center_id']);
        } elseif (! empty($data['diagnostic_center'])) {
            $center = DiagnosticCenter::where('name', 'like', "%{$data['diagnostic_center']}%")->first();
        }

        $centerName = $center ? $center->name : ($data['diagnostic_center'] ?? 'General Diagnostic Lab');
        $commissionRatePercent = $center ? (float) $center->commission_rate : 15.00;
        $discountRatePercent = $center ? (float) $center->discount_rate : 0.00;

        $platformCommission = round($data['total_amount'] * ($commissionRatePercent / 100), 2);
        $patientDiscount = round($data['total_amount'] * ($discountRatePercent / 100), 2);

        $order = DiagnosticOrder::create([
            'order_number' => DiagnosticOrder::generateOrderNumber(),
            'patient_id' => $patientId,
            'doctor_id' => $data['doctor_id'] ?? null,
            'prescription_id' => $data['prescription_id'] ?? null,
            'diagnostic_center_id' => $center?->id,
            'diagnostic_center' => $centerName,
            'test_names' => $data['test_names'],
            'total_amount' => $data['total_amount'],
            'commission_rate' => $commissionRatePercent,
            'platform_commission' => $platformCommission,
            'patient_discount' => $patientDiscount,
            'sample_collection_type' => $data['sample_collection_type'],
            'collection_address' => $data['collection_address'] ?? null,
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'] ?? 'Morning (8:00 AM - 11:00 AM)',
            'contact_phone' => $data['contact_phone'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "ডায়াগনস্টিক টেস্টের স্যাম্পল কালেকশন বুকিং সফলভাবে গ্রহণ করা হয়েছে! অর্ডার আইডি: {$order->order_number} (স্ট্যাটাস: অপেক্ষমাণ)",
            'order' => $order->load(['diagnosticCenter']),
        ], 201);
    }

    /**
     * Admin: Update diagnostic order status.
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        if (! $isAdmin) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস। শুধুমাত্র অ্যাডমিন এই অ্যাকশন সম্পন্ন করতে পারেন।'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,sample_collected,report_ready,completed,cancelled',
        ]);

        $order = DiagnosticOrder::findOrFail($id);
        $order->status = $data['status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => "অর্ডার #{$order->order_number}-এর স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।",
            'order' => $order,
        ]);
    }
}
