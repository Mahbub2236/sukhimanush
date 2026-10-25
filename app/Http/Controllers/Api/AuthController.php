<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Unified Login for Super Admin, Doctor, and Patient.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $credentials['email'];
        $candidates = array_unique([
            $email,
            str_replace(['@sukhimanush.', '@mahriva.'], '@novena.', $email),
            str_replace(['@novena.', '@sukhimanush.'], '@mahriva.', $email),
            str_replace(['@novena.', '@mahriva.'], '@sukhimanush.', $email),
        ]);

        $user = User::whereIn('email', $candidates)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        $doctor = null;
        if ($user->isDoctor()) {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ((! $user->is_approved || ($doctor && ! $doctor->is_approved)) && ! in_array($user->role, ['admin', 'super_admin'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'আপনার ডাক্তার অ্যাকাউন্টটি এখনও অ্যাডমিন কর্তৃক যাচাই ও অনুমোদন করা হয়নি। অনুমোদনের পর আপনি লগইন করতে পারবেন।',
                ], 403);
            }
        }

        if ($user->role === 'agent' && ! $user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'আপনার গ্রামীণ ডিজিটাল হেলথ পয়েন্টটি এখনও অ্যাডমিন কর্তৃক অনুমোদিত হয়নি। অনুগ্রহ করে অনুমোদনের অপেক্ষা করুন।',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_approved' => (bool) $user->is_approved,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'pharmacy_name' => $user->pharmacy_name,
                'agent_commission_balance' => (float) $user->agent_commission_balance,
            ],
            'doctor' => $doctor,
            'token' => $token,
        ]);
    }

    /**
     * User / Patient / Doctor / Rural Agent Self-Registration.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:patient,doctor,agent',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'pharmacy_name' => 'nullable|string|max:150',
            // Doctor specific fields if role === 'doctor'
            'specialization' => 'required_if:role,doctor|string|nullable',
            'designation' => 'required_if:role,doctor|string|nullable',
            'qualifications' => 'nullable|string',
            'bmdc_number' => 'nullable|string|max:50',
            'division' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:50',
            'chamber_address' => 'required_if:role,doctor|string|nullable',
            'city' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric',
            'telemedicine_fee' => 'nullable|numeric',
        ]);

        // Patients are auto-approved. Doctors and Rural Pharmacy Agents require Admin verification & approval.
        $isApproved = ($data['role'] === 'patient');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_approved' => $isApproved,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'pharmacy_name' => $data['role'] === 'agent' ? ($data['pharmacy_name'] ?? ($data['name'].' ডিজিটাল হেলথ পয়েন্ট')) : null,
            'agent_commission_balance' => 0.00,
        ]);

        $doctor = null;
        if ($data['role'] === 'doctor') {
            $division = $data['division'] ?? 'Dhaka';
            $district = $data['district'] ?? 'Dhaka';

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'slug' => Str::slug($user->name.'-'.uniqid()),
                'bmdc_number' => $data['bmdc_number'] ?? ('BMDC-A'.rand(10000, 99999)),
                'specialization' => $data['specialization'] ?? 'General Medicine',
                'designation' => $data['designation'] ?? 'Medical Consultant',
                'qualifications' => $data['qualifications'] ?? 'MBBS',
                'chamber_address' => $data['chamber_address'] ?? "Chamber, {$district}",
                'division' => $division,
                'district' => $district,
                'city' => $district,
                'consultation_fee' => $data['consultation_fee'] ?? 50.00,
                'telemedicine_fee' => $data['telemedicine_fee'] ?? ($data['consultation_fee'] ?? 50.00),
                'email' => $user->email,
                'phone' => $user->phone,
                'available_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_duration_minutes' => 30,
                'is_approved' => false, // Requires Admin verification and approval
                'is_verified' => false, // Requires Admin verification of BMDC
                'is_emergency_available' => true,
            ]);
        }

        $token = $isApproved ? $user->createToken('auth_token')->plainTextToken : null;

        $message = 'নিবন্ধন সফলভাবে সম্পন্ন হয়েছে!';
        if ($data['role'] === 'doctor') {
            $message = 'ডাক্তার নিবন্ধন সফল হয়েছে। আপনার প্রোফাইলটি বর্তমানে অ্যাডমিনের অনুমোদনের অপেক্ষায় রয়েছে (Pending Approval)।';
        } elseif ($data['role'] === 'agent') {
            $message = 'এজেন্ট নিবন্ধন সফল হয়েছে। আপনার পয়েন্টটি বর্তমানে অ্যাডমিনের অনুমোদনের অপেক্ষায় রয়েছে (Pending Approval)।';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_approved' => $isApproved,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_approved' => (bool) $user->is_approved,
                'phone' => $user->phone,
                'pharmacy_name' => $user->pharmacy_name,
                'agent_commission_balance' => 0.00,
            ],
            'doctor' => $doctor,
            'token' => $token,
        ], 201);
    }

    /**
     * Get currently authenticated user details.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $doctor = null;
        if (method_exists($user, 'isDoctor') && $user->isDoctor()) {
            $doctor = Doctor::where('user_id', $user->id)->first();
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'doctor' => $doctor,
        ]);
    }

    /**
     * Revoke Sanctum Token on Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'সফলভাবে লগআউট করা হয়েছে।',
        ]);
    }
}
