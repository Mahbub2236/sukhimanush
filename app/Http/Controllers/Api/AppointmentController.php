<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Get dynamic available time slots for doctor & date.
     */
    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        $data = $this->appointmentService->getAvailableSlots(
            (int) $request->input('doctor_id'),
            $request->input('date')
        );

        return response()->json($data);
    }

    /**
     * Store a validated appointment.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->bookAppointment($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully!',
                'appointment' => $appointment,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Track live appointment status.
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $appointments = $this->appointmentService->trackAppointment($request->input('query'));

        return response()->json([
            'success' => true,
            'count' => $appointments->count(),
            'appointments' => $appointments,
        ]);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(string $appointmentNumber, Request $request): JsonResponse
    {
        $appointment = Appointment::where('appointment_number', $appointmentNumber)->firstOrFail();

        if ($appointment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed appointments cannot be cancelled.',
            ], 400);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => "Appointment {$appointmentNumber} has been successfully cancelled.",
            'appointment' => $appointment->fresh(['doctor.department', 'department']),
        ]);
    }
}
