<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloodSosController extends Controller
{
    /**
     * Get list of emergency blood requests.
     */
    public function requests(Request $request): JsonResponse
    {
        $group = $request->input('blood_group');
        $district = $request->input('district');

        $query = BloodRequest::where('status', 'open')
            ->orderBy('created_at', 'desc');

        if ($group) {
            $query->where('blood_group', $group);
        }
        if ($district) {
            $query->where('district', 'like', "%{$district}%");
        }

        $requests = $query->take(20)->get();

        $stats = [
            'open_requests' => BloodRequest::where('status', 'open')->count(),
            'total_donors' => BloodDonor::where('is_available', true)->count(),
            'fulfilled_today' => BloodRequest::where('status', 'fulfilled')->whereDate('updated_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'requests' => $requests,
        ]);
    }

    /**
     * Post an urgent Blood / Platelet SOS request.
     */
    public function storeRequest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_name' => 'required|string|max:120',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'component' => 'required|in:whole_blood,platelets,plasma,rbc',
            'units_needed' => 'required|integer|min:1|max:10',
            'hospital_name' => 'required|string|max:180',
            'district' => 'required|string|max:80',
            'upazila' => 'nullable|string|max:80',
            'contact_name' => 'required|string|max:100',
            'contact_phone' => 'required|string|max:25',
            'urgency' => 'required|in:critical_immediate,within_12h,within_24h,scheduled',
            'reason' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $code = 'SOS-'.strtoupper(str_replace('+', 'P', str_replace('-', 'N', $data['blood_group']))).'-'.rand(1000, 9999);

        $bloodRequest = BloodRequest::create([
            'request_code' => $code,
            'user_id' => $data['user_id'] ?? null,
            'patient_name' => $data['patient_name'],
            'blood_group' => $data['blood_group'],
            'component' => $data['component'],
            'units_needed' => $data['units_needed'],
            'hospital_name' => $data['hospital_name'],
            'district' => $data['district'],
            'upazila' => $data['upazila'] ?? null,
            'contact_name' => $data['contact_name'],
            'contact_phone' => $data['contact_phone'],
            'urgency' => $data['urgency'],
            'reason' => $data['reason'] ?? null,
            'status' => 'open',
        ]);

        // Find matching nearby voluntary donors
        $matchingDonorsCount = BloodDonor::where('blood_group', $data['blood_group'])
            ->where('is_available', true)
            ->where('district', $data['district'])
            ->count();

        return response()->json([
            'success' => true,
            'message' => "জরুরি ব্লাড SOS পোস্ট সফল হয়েছে! ট্র্যাকিং কোড: {$code}",
            'request' => $bloodRequest,
            'matching_donors_count' => $matchingDonorsCount,
        ]);
    }

    /**
     * Search voluntary blood donors with privacy protection.
     */
    public function donors(Request $request): JsonResponse
    {
        $group = $request->input('blood_group');
        $district = $request->input('district');

        $query = BloodDonor::where('is_available', true);

        if ($group) {
            $query->where('blood_group', $group);
        }
        if ($district) {
            $query->where('district', 'like', "%{$district}%");
        }

        $donors = $query->orderBy('total_donations', 'desc')->take(25)->get();

        return response()->json([
            'success' => true,
            'donors' => $donors,
        ]);
    }

    /**
     * Register as voluntary blood donor.
     */
    public function registerDonor(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'district' => 'required|string|max:80',
            'upazila' => 'nullable|string|max:80',
            'phone' => 'required|string|max:25',
            'last_donated_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $donor = BloodDonor::updateOrCreate(
            ['phone' => $data['phone']],
            [
                'user_id' => $data['user_id'] ?? null,
                'name' => $data['name'],
                'blood_group' => $data['blood_group'],
                'district' => $data['district'],
                'upazila' => $data['upazila'] ?? null,
                'last_donated_date' => $data['last_donated_date'] ?? null,
                'is_available' => true,
                'badge' => 'life_saver',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'অভিনন্দন! আপনি সফলভাবে স্বেচ্ছাসেবী রক্তদাতা হিসেবে যুক্ত হয়েছেন।',
            'donor' => $donor,
        ]);
    }

    /**
     * Donor responds "I CAN DONATE" to an SOS.
     */
    public function respondToSos(int $id, Request $request): JsonResponse
    {
        $bloodRequest = BloodRequest::findOrFail($id);

        $donorPhone = $request->input('donor_phone', '');
        $donorName = $request->input('donor_name', 'স্বেচ্ছাসেবী রক্তদাতা');

        return response()->json([
            'success' => true,
            'message' => "ধন্যবাদ {$donorName}! রোগীর স্বজনের সাথে যোগাযোগ করার জন্য ফোন নম্বর নিচে দেওয়া হলো।",
            'patient_contact' => [
                'name' => $bloodRequest->contact_name,
                'phone' => $bloodRequest->contact_phone,
                'hospital' => $bloodRequest->hospital_name,
            ],
        ]);
    }
}
