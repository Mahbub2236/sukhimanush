<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::with('department')->get();

        return response()->json($services);
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::where('slug', $slug)
            ->with('department')
            ->firstOrFail();

        return response()->json($service);
    }
}
