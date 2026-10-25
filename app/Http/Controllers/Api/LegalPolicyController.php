<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegalPolicyController extends Controller
{
    /**
     * Public: List all active policies summary
     */
    public function index(): JsonResponse
    {
        $policies = LegalPolicy::active()
            ->select(['key', 'title', 'title_en', 'subtitle', 'summary', 'effective_date', 'last_reviewed_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $policies,
        ]);
    }

    /**
     * Public: Get full policy by key (e.g. privacy_policy, terms_of_service)
     */
    public function show(string $key): JsonResponse
    {
        $policy = LegalPolicy::where('key', $key)->first();

        if (! $policy) {
            // Check default policies
            $defaults = LegalPolicy::getDefaultPolicies();
            if (isset($defaults[$key])) {
                $policy = LegalPolicy::create($defaults[$key]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Policy document not found.',
                ], 404);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $policy,
        ]);
    }

    /**
     * Admin: Get all policies for Super Admin dashboard management
     */
    public function adminIndex(): JsonResponse
    {
        $policies = LegalPolicy::orderBy('id', 'asc')->get();

        // If none seeded, seed defaults
        if ($policies->isEmpty()) {
            foreach (LegalPolicy::getDefaultPolicies() as $defaultData) {
                LegalPolicy::create($defaultData);
            }
            $policies = LegalPolicy::orderBy('id', 'asc')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $policies,
        ]);
    }

    /**
     * Admin: Update policy content, sections, and metadata
     */
    public function adminUpdate(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|string',
            'sections.*.title' => 'required|string',
            'sections.*.icon' => 'nullable|string',
            'sections.*.summary' => 'nullable|string',
            'sections.*.content' => 'required|string',
            'effective_date' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'meta' => 'nullable|array',
        ]);

        $policy = LegalPolicy::where('key', $key)->first();

        if (! $policy) {
            $policy = new LegalPolicy(['key' => $key]);
        }

        $policy->fill($validated);
        $policy->last_reviewed_at = now();
        $policy->save();

        return response()->json([
            'success' => true,
            'message' => 'পলিসি সফলভাবে আপডেট করা হয়েছে (Legal Policy updated successfully).',
            'data' => $policy,
        ]);
    }

    /**
     * Admin: Reset policy to government-compliant default draft
     */
    public function adminResetDefault(string $key): JsonResponse
    {
        $defaults = LegalPolicy::getDefaultPolicies();

        if (! isset($defaults[$key])) {
            return response()->json([
                'success' => false,
                'message' => 'Default draft for this policy key is not available.',
            ], 404);
        }

        $defaultData = $defaults[$key];
        $defaultData['last_reviewed_at'] = now();

        $policy = LegalPolicy::updateOrCreate(
            ['key' => $key],
            $defaultData
        );

        return response()->json([
            'success' => true,
            'message' => 'বাংলাদেশ স্বাস্থ্য ও বিএমডিসি নীতিমালার সাথে সংগতি রেখে ডিফল্ট ড্রাফটে রিস্টোর করা হয়েছে।',
            'data' => $policy,
        ]);
    }
}
