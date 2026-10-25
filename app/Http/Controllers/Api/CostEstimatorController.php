<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcedureCost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CostEstimatorController extends Controller
{
    /**
     * Search and list medical procedures with transparent cost benchmarks.
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        $category = $request->input('category');

        $query = ProcedureCost::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name_bn', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $procedures = $query->orderBy('name_bn')->get();

        $categories = ProcedureCost::select('category')->distinct()->pluck('category');

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'procedures' => $procedures,
        ]);
    }

    /**
     * Detailed procedure breakdown.
     */
    public function show(int $id): JsonResponse
    {
        $procedure = ProcedureCost::findOrFail($id);

        return response()->json([
            'success' => true,
            'procedure' => $procedure,
        ]);
    }
}
