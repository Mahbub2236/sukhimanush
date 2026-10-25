<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Get doctors with optional department and keyword filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::with('chambers');

        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', "%{$request->input('specialization')}%");
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('qualifications', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $doctors = $query->orderBy('name')->get();

        return response()->json($doctors);
    }

    /**
     * Get single doctor detail with chambers.
     */
    public function show(string $slug): JsonResponse
    {
        $doctor = Doctor::where('slug', $slug)
            ->with('chambers')
            ->firstOrFail();

        return response()->json($doctor);
    }

    /**
     * Get live available time slots for doctor.
     */
    public function slots(int $id, Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $data = $appointmentService->getAvailableSlots($id, $date);

        return response()->json($data);
    }
}
