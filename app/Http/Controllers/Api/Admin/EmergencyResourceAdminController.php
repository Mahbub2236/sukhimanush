<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceProvider;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\EmergencyPassportToken;
use App\Models\HospitalBed;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmergencyResourceAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Ambulance Management CRUD
    |--------------------------------------------------------------------------
    */
    public function ambulances(Request $request): JsonResponse
    {
        $query = AmbulanceProvider::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('service_name', 'like', "%{$s}%")
                    ->orWhere('driver_or_contact_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%");
            });
        }

        if ($request->filled('vehicle_type') && $request->input('vehicle_type') !== 'all') {
            $query->where('vehicle_type', $request->input('vehicle_type'));
        }

        $ambulances = $query->orderByDesc('id')->paginate($request->input('per_page', 15));

        return response()->json($ambulances);
    }

    public function storeAmbulance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'driver_or_contact_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|in:ac,non_ac,icu,freezer',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'is_available_24_7' => 'boolean',
        ]);

        $ambulance = AmbulanceProvider::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Ambulance service added successfully.',
            'ambulance' => $ambulance,
        ], 201);
    }

    public function updateAmbulance(int $id, Request $request): JsonResponse
    {
        $ambulance = AmbulanceProvider::findOrFail($id);

        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'driver_or_contact_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|in:ac,non_ac,icu,freezer',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'is_available_24_7' => 'boolean',
        ]);

        $ambulance->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Ambulance service updated successfully.',
            'ambulance' => $ambulance,
        ]);
    }

    public function destroyAmbulance(int $id): JsonResponse
    {
        $ambulance = AmbulanceProvider::findOrFail($id);
        $ambulance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ambulance service deleted successfully.',
        ]);
    }

    public function toggleAmbulanceStatus(int $id): JsonResponse
    {
        $ambulance = AmbulanceProvider::findOrFail($id);
        $ambulance->is_available_24_7 = ! $ambulance->is_available_24_7;
        $ambulance->save();

        return response()->json([
            'success' => true,
            'message' => 'Ambulance status updated.',
            'is_available_24_7' => $ambulance->is_available_24_7,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hospital ICU Bed Management CRUD
    |--------------------------------------------------------------------------
    */
    public function hospitalBeds(Request $request): JsonResponse
    {
        $query = HospitalBed::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('hospital_name', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%")
                    ->orWhere('hotline', 'like', "%{$s}%")
                    ->orWhere('emergency_phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('hospital_type') && $request->input('hospital_type') !== 'all') {
            $query->where('hospital_type', $request->input('hospital_type'));
        }

        $beds = $query->orderByDesc('icu_available')->paginate($request->input('per_page', 15));

        return response()->json($beds);
    }

    public function storeHospitalBed(Request $request): JsonResponse
    {
        $data = $request->validate([
            'hospital_name' => 'required|string|max:150',
            'hospital_type' => 'required|in:government,private,specialized',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'hotline' => 'nullable|string|max:30',
            'emergency_phone' => 'required|string|max:30',
            'icu_available' => 'required|integer|min:0',
            'icu_total' => 'required|integer|min:0',
            'ccu_available' => 'nullable|integer|min:0',
            'nicu_available' => 'nullable|integer|min:0',
            'general_beds_available' => 'nullable|integer|min:0',
        ]);

        $data['last_verified_at'] = now();
        $bed = HospitalBed::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Hospital ICU Bed entry created.',
            'hospital_bed' => $bed,
        ], 201);
    }

    public function updateHospitalBed(int $id, Request $request): JsonResponse
    {
        $bed = HospitalBed::findOrFail($id);

        $data = $request->validate([
            'hospital_name' => 'required|string|max:150',
            'hospital_type' => 'required|in:government,private,specialized',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'hotline' => 'nullable|string|max:30',
            'emergency_phone' => 'required|string|max:30',
            'icu_available' => 'required|integer|min:0',
            'icu_total' => 'required|integer|min:0',
            'ccu_available' => 'nullable|integer|min:0',
            'nicu_available' => 'nullable|integer|min:0',
            'general_beds_available' => 'nullable|integer|min:0',
        ]);

        $data['last_verified_at'] = now();
        $bed->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Hospital Bed status updated and verified.',
            'hospital_bed' => $bed,
        ]);
    }

    public function destroyHospitalBed(int $id): JsonResponse
    {
        $bed = HospitalBed::findOrFail($id);
        $bed->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hospital record deleted successfully.',
        ]);
    }

    public function quickVerifyHospitalBed(int $id): JsonResponse
    {
        $bed = HospitalBed::findOrFail($id);
        $bed->last_verified_at = now();
        $bed->save();

        return response()->json([
            'success' => true,
            'message' => 'Hospital availability status verified as of now.',
            'last_verified_at' => $bed->last_verified_at->toIso8601String(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Blood SOS & Donors Management CRUD
    |--------------------------------------------------------------------------
    */
    public function bloodRequests(Request $request): JsonResponse
    {
        $query = BloodRequest::with('user');

        if ($request->filled('blood_group') && $request->input('blood_group') !== 'all') {
            $query->where('blood_group', $request->input('blood_group'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('patient_name', 'like', "%{$s}%")
                    ->orWhere('hospital_name', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%")
                    ->orWhere('request_code', 'like', "%{$s}%")
                    ->orWhere('contact_phone', 'like', "%{$s}%");
            });
        }

        $requests = $query->orderByDesc('id')->paginate($request->input('per_page', 15));

        return response()->json($requests);
    }

    public function updateBloodRequestStatus(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:open,fulfilled,cancelled',
        ]);

        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->update(['status' => $data['status']]);

        return response()->json([
            'success' => true,
            'message' => "Blood request marked as {$data['status']}.",
            'request' => $bloodRequest,
        ]);
    }

    public function destroyBloodRequest(int $id): JsonResponse
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blood request deleted successfully.',
        ]);
    }

    public function bloodDonors(Request $request): JsonResponse
    {
        $query = BloodDonor::with('user');

        if ($request->filled('blood_group') && $request->input('blood_group') !== 'all') {
            $query->where('blood_group', $request->input('blood_group'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('donor_name', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $donors = $query->orderByDesc('id')->paginate($request->input('per_page', 15));

        return response()->json($donors);
    }

    public function toggleDonorAvailability(int $id): JsonResponse
    {
        $donor = BloodDonor::findOrFail($id);
        $donor->is_available = ! $donor->is_available;
        $donor->save();

        return response()->json([
            'success' => true,
            'message' => 'Donor availability updated.',
            'is_available' => $donor->is_available,
        ]);
    }

    public function destroyBloodDonor(int $id): JsonResponse
    {
        $donor = BloodDonor::findOrFail($id);
        $donor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blood donor record deleted.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Digital Health Passport Administration
    |--------------------------------------------------------------------------
    */
    public function healthPassports(Request $request): JsonResponse
    {
        $query = EmergencyPassportToken::with(['user' => function ($q) {
            $q->select('id', 'name', 'email', 'phone', 'blood_group', 'emergency_contact_phone', 'chronic_conditions', 'allergies');
        }]);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('blood_group', 'like', "%{$s}%");
            })->orWhere('token', 'like', "%{$s}%");
        }

        $passports = $query->orderByDesc('updated_at')->paginate($request->input('per_page', 15));

        return response()->json($passports);
    }

    public function regeneratePassportToken(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $newToken = Str::random(32);

        $passport = EmergencyPassportToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => $newToken,
                'expires_at' => now()->addYears(2),
                'view_count' => 0,
                'last_accessed_at' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Emergency Health Passport token regenerated successfully.',
            'passport' => $passport,
        ]);
    }
}
