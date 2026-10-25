<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticCenterController extends Controller
{
    /**
     * List all active partner diagnostic centers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DiagnosticCenter::where('is_active', true)
            ->where('is_approved', true);

        if ($request->filled('district') && $request->input('district') !== 'all') {
            $query->where('district', $request->input('district'));
        }

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%");
            });
        }

        $centers = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'centers' => $centers,
        ]);
    }

    /**
     * Get a specific diagnostic center by ID or Slug.
     */
    public function show(string $idOrSlug): JsonResponse
    {
        $center = DiagnosticCenter::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'center' => $center,
        ]);
    }

    /**
     * Diagnostic Center Partner Registration (Online Onboarding).
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:200',
            'license_number' => 'nullable|string|max:100',
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'nullable|string',
            'address' => 'required|string|max:500',
            'contact_phone' => 'required|string|min:10|max:20',
            'contact_email' => 'nullable|email|max:100',
            'commission_rate' => 'nullable|numeric|min:5|max:50', // Default agreed commission %
            'discount_rate' => 'nullable|numeric|min:0|max:50',
            'home_collection_available' => 'nullable|boolean',
            'home_collection_fee' => 'nullable|numeric|min:0',
            'available_tests' => 'nullable|array',
        ]);

        $slug = DiagnosticCenter::generateSlug($data['name']);

        $center = DiagnosticCenter::create([
            'name' => $data['name'],
            'slug' => $slug,
            'license_number' => $data['license_number'] ?? null,
            'division' => $data['division'],
            'district' => $data['district'],
            'upazila' => $data['upazila'] ?? null,
            'address' => $data['address'],
            'contact_phone' => $data['contact_phone'],
            'contact_email' => $data['contact_email'] ?? null,
            'commission_rate' => $data['commission_rate'] ?? 15.00,
            'discount_rate' => $data['discount_rate'] ?? 10.00,
            'home_collection_available' => $data['home_collection_available'] ?? true,
            'home_collection_fee' => $data['home_collection_fee'] ?? 100.00,
            'available_tests' => $data['available_tests'] ?? [
                ['name' => 'Complete Blood Count (CBC)', 'price' => 400.00, 'category' => 'Hematology'],
                ['name' => 'Fasting Blood Sugar (FBS)', 'price' => 150.00, 'category' => 'Biochemistry'],
                ['name' => 'Serum Lipid Profile', 'price' => 1000.00, 'category' => 'Biochemistry'],
                ['name' => 'Serum Creatinine', 'price' => 400.00, 'category' => 'Biochemistry'],
                ['name' => 'Urine Routine Examination (R/E)', 'price' => 250.00, 'category' => 'Clinical Pathology'],
            ],
            'is_approved' => true, // Instant live for demo / can be pending in prod
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ডায়াগনস্টিক সেন্টার সফলভাবে নিবন্ধিত হয়েছে! পার্টনার নেটওয়ার্কে যুক্ত হওয়ার জন্য ধন্যবাদ।',
            'center' => $center,
        ], 201);
    }

    /**
     * Admin: Update commission rate and approval status.
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $center = DiagnosticCenter::findOrFail($id);

        $data = $request->validate([
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'home_collection_available' => 'nullable|boolean',
            'home_collection_fee' => 'nullable|numeric|min:0',
            'is_approved' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'available_tests' => 'nullable|array',
        ]);

        $center->update($data);

        return response()->json([
            'success' => true,
            'message' => 'ডায়াগনস্টিক সেন্টারের কমিশন ও সেটিংস সফলভাবে আপডেট করা হয়েছে!',
            'center' => $center,
        ]);
    }
}
