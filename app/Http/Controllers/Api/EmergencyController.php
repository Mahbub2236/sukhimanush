<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceProvider;
use App\Models\HospitalBed;
use App\Services\GeoDistanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    /**
     * Emergency Hub overview stats and quick resources.
     */
    public function overview(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'hotlines' => [
                ['name' => 'জাতীয় জরুরি সেবা', 'number' => '999', 'type' => 'Police & Ambulance'],
                ['name' => 'স্বাস্থ্য বাতায়ন', 'number' => '16263', 'type' => 'Govt Health Helpline'],
                ['name' => 'আইইডিসিআর কন্ট্রোল রুম', 'number' => '10655', 'type' => 'Infectious Disease'],
                ['name' => 'রেড ক্রিসেন্ট ব্লাড ব্যাংক', 'number' => '02-9330188', 'type' => 'Emergency Blood'],
            ],
            'available_ambulances_count' => AmbulanceProvider::where('is_available_24_7', true)->count(),
            'total_icu_beds_available' => HospitalBed::sum('icu_available'),
        ]);
    }

    /**
     * Search and discover nearby verified ambulances with patient-to-ambulance distance calculation.
     */
    public function ambulances(Request $request): JsonResponse
    {
        $district = $request->input('district');
        $type = $request->input('vehicle_type');
        $patientLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $patientLon = $request->filled('lng') ? (float) $request->input('lng') : null;

        $query = AmbulanceProvider::where('is_available_24_7', true);

        if ($district && $district !== 'all') {
            $query->where('district', 'like', "%{$district}%");
        }
        if ($type) {
            $query->where('vehicle_type', $type);
        }

        $ambulances = $query->get()->map(function ($amb) use ($patientLat, $patientLon, $district) {
            $distance = GeoDistanceService::calculateDistance(
                $patientLat,
                $patientLon,
                $district,
                $amb->district,
                null,
                null,
                true // isAmbulance
            );

            $ambArray = $amb->toArray();
            $ambArray['distance_km'] = $distance['distance_km'];
            $ambArray['time_minutes'] = $distance['time_minutes'];
            $ambArray['distance_text'] = $distance['distance_text'];
            $ambArray['time_text'] = $distance['time_text'];

            return $ambArray;
        });

        // Sort by nearest ambulance first
        $sortedAmbulances = $ambulances->sortBy('distance_km')->values();

        return response()->json([
            'success' => true,
            'ambulances' => $sortedAmbulances,
        ]);
    }

    /**
     * Live Hospital ICU / CCU / NICU Bed Availability Radar.
     */
    public function hospitalBeds(Request $request): JsonResponse
    {
        $district = $request->input('district');

        $query = HospitalBed::query();

        if ($district) {
            $query->where('district', 'like', "%{$district}%");
        }

        $beds = $query->orderBy('icu_available', 'desc')->get();

        return response()->json([
            'success' => true,
            'total_icu_free' => $beds->sum('icu_available'),
            'total_nicu_free' => $beds->sum('nicu_available'),
            'total_ccu_free' => $beds->sum('ccu_available'),
            'hospitals' => $beds,
        ]);
    }
}
