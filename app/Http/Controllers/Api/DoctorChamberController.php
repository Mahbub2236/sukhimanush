<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorChamberRequest;
use App\Http\Requests\UpdateDoctorChamberRequest;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\DoctorChamber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorChamberController extends Controller
{
    /**
     * Resolve and authorize doctor for chamber management.
     */
    private function authorizeDoctor(Request $request, ?int $chamberDoctorId = null): ?Doctor
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $isAdmin = in_array($user->role ?? '', ['admin', 'super_admin'], true);

        if ($user instanceof DoctorAssistant) {
            $doctor = $user->doctor ?: Doctor::find($user->doctor_id);
            if ($chamberDoctorId && $doctor && $doctor->id !== $chamberDoctorId && ! $isAdmin) {
                return null;
            }

            return $doctor;
        }

        if ($isAdmin) {
            $targetDocId = $chamberDoctorId ?: (int) $request->input('doctor_id');

            return $targetDocId ? Doctor::find($targetDocId) : Doctor::first();
        }

        $doctor = Doctor::where('user_id', $user->id)->first();
        if (! $doctor) {
            return null;
        }

        if ($chamberDoctorId && $doctor->id !== $chamberDoctorId) {
            return null;
        }

        return $doctor;
    }

    /**
     * Get all chambers for a specific doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdmin = in_array($user->role ?? '', ['admin', 'super_admin'], true);
        $doctorId = $request->input('doctor_id');

        if (! $doctorId && $user) {
            if ($user instanceof DoctorAssistant) {
                $doctorId = $user->doctor_id;
            } else {
                $myDoc = Doctor::where('user_id', $user->id)->first();
                $doctorId = $myDoc?->id;
            }
        }

        if (! $doctorId) {
            return response()->json(['message' => 'Doctor ID is required.'], 400);
        }

        $doctor = Doctor::findOrFail($doctorId);
        $chambers = $doctor->chambers()->orderByDesc('is_primary')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'chambers' => $chambers,
        ]);
    }

    /**
     * Add a new chamber for the doctor.
     */
    public function store(StoreDoctorChamberRequest $request): JsonResponse
    {
        $doctor = $this->authorizeDoctor($request);
        if (! $doctor) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাকশন! ডাক্তারের প্রোফাইল শনাক্ত করা যায়নি।',
            ], 403);
        }

        $validated = $request->validated();

        $isPrimary = ! empty($validated['is_primary']);

        // If this is the doctor's first chamber, make it primary automatically
        if ($doctor->chambers()->count() === 0) {
            $isPrimary = true;
        }

        if ($isPrimary) {
            $doctor->chambers()->update(['is_primary' => false]);
        }

        $chamber = $doctor->chambers()->create([
            'chamber_name' => $validated['chamber_name'],
            'branch_name' => $validated['branch_name'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'] ?? $doctor->city,
            'district' => $validated['district'] ?? $doctor->district,
            'upazila' => $validated['upazila'] ?? $doctor->upazila,
            'phone' => $validated['phone'] ?? $doctor->phone,
            'visiting_days' => $validated['visiting_days'] ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'start_time' => $validated['start_time'] ?? '17:00:00',
            'end_time' => $validated['end_time'] ?? '21:00:00',
            'slot_duration_minutes' => $validated['slot_duration_minutes'] ?? 20,
            'consultation_fee' => $validated['consultation_fee'] ?? $doctor->consultation_fee,
            'is_primary' => $isPrimary,
            'is_active' => true,
        ]);

        if ($isPrimary) {
            $doctor->update(['chamber_address' => $chamber->address]);
        }

        return response()->json([
            'success' => true,
            'message' => 'নতুন চেম্বার সফলভাবে যুক্ত করা হয়েছে!',
            'chamber' => $chamber,
            'chambers' => $doctor->chambers()->orderByDesc('is_primary')->orderBy('id')->get(),
        ]);
    }

    /**
     * Update an existing chamber.
     */
    public function update(int $id, UpdateDoctorChamberRequest $request): JsonResponse
    {
        $chamber = DoctorChamber::findOrFail($id);
        $doctor = $this->authorizeDoctor($request, $chamber->doctor_id);

        if (! $doctor) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের নির্ধারিত চেম্বার আপডেট করতে পারেন।',
            ], 403);
        }

        $validated = $request->validated();

        if (! empty($validated['is_primary'])) {
            DoctorChamber::where('doctor_id', $chamber->doctor_id)->where('id', '!=', $id)->update(['is_primary' => false]);
            $chamber->doctor->update(['chamber_address' => $validated['address']]);
        }

        $chamber->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'চেম্বার তথ্য সফলভাবে আপডেট হয়েছে!',
            'chamber' => $chamber->fresh(),
            'chambers' => DoctorChamber::where('doctor_id', $chamber->doctor_id)->orderByDesc('is_primary')->orderBy('id')->get(),
        ]);
    }

    /**
     * Delete a chamber.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $chamber = DoctorChamber::findOrFail($id);
        $doctor = $this->authorizeDoctor($request, $chamber->doctor_id);

        if (! $doctor) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের নির্ধারিত চেম্বার মুছতে পারেন।',
            ], 403);
        }

        $docId = $chamber->doctor_id;
        $totalChambers = DoctorChamber::where('doctor_id', $docId)->count();

        if ($totalChambers <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'অন্তত একটি চেম্বার থাকা আবশ্যক। এটি মুছে ফেলা যাবে না।',
            ], 422);
        }

        $wasPrimary = $chamber->is_primary;
        $chamber->delete();

        // If the deleted chamber was primary, assign primary to the first remaining chamber
        if ($wasPrimary) {
            $nextPrimary = DoctorChamber::where('doctor_id', $docId)->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
                $nextPrimary->doctor->update(['chamber_address' => $nextPrimary->address]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'চেম্বারটি সফলভাবে মুছে ফেলা হয়েছে!',
            'chambers' => DoctorChamber::where('doctor_id', $docId)->orderByDesc('is_primary')->orderBy('id')->get(),
        ]);
    }

    /**
     * Set a specific chamber as primary.
     */
    public function setPrimary(int $id, Request $request): JsonResponse
    {
        $chamber = DoctorChamber::findOrFail($id);
        $doctor = $this->authorizeDoctor($request, $chamber->doctor_id);

        if (! $doctor) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের চেম্বার প্রধান চেম্বার হিসেবে সেট করতে পারেন।',
            ], 403);
        }

        DoctorChamber::where('doctor_id', $chamber->doctor_id)->update(['is_primary' => false]);
        $chamber->update(['is_primary' => true]);
        $chamber->doctor->update(['chamber_address' => $chamber->address]);

        return response()->json([
            'success' => true,
            'message' => 'প্রধান চেম্বার হিসেবে সফলভাবে সেট করা হয়েছে!',
            'chambers' => DoctorChamber::where('doctor_id', $chamber->doctor_id)->orderByDesc('is_primary')->orderBy('id')->get(),
        ]);
    }
}
