<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Get all active departments with doctor count.
     */
    public function index(): JsonResponse
    {
        $departments = Department::active()
            ->withCount(['doctors', 'services'])
            ->get();

        return response()->json($departments);
    }

    /**
     * Get single department by slug with doctors and services.
     */
    public function show(string $slug): JsonResponse
    {
        $department = Department::where('slug', $slug)
            ->with(['doctors', 'services'])
            ->firstOrFail();

        return response()->json($department);
    }
}
