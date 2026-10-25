<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyPassportToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HealthPassportController extends Controller
{
    /**
     * Get or generate a secure Emergency QR token for patient's health passport.
     */
    public function getOrCreateToken(int $userId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        if (! $authUser || (! $isAdmin && $authUser->id !== $userId)) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $user = User::findOrFail($userId);

        $passportToken = EmergencyPassportToken::firstOrCreate(
            ['user_id' => $userId],
            [
                'token' => Str::random(32),
                'expires_at' => now()->addYear(),
            ]
        );

        $baseUrl = url('/');
        $emergencyUrl = "{$baseUrl}/emergency-pass/{$passportToken->token}";

        return response()->json([
            'success' => true,
            'token' => $passportToken->token,
            'emergency_url' => $emergencyUrl,
            'view_count' => $passportToken->view_count,
            'last_accessed_at' => $passportToken->last_accessed_at,
        ]);
    }

    /**
     * Controlled-access Emergency Health Profile (opened via QR code in emergency room).
     */
    public function viewEmergencyPass(string $token): JsonResponse
    {
        $passportToken = EmergencyPassportToken::where('token', $token)->first();

        if (! $passportToken) {
            return response()->json([
                'success' => false,
                'message' => 'দুঃখিত, এই জরুরি হেলথ কার্ডের মেয়াদ শেষ অথবা কিউআর কোডটি সঠিক নয়।',
            ], 404);
        }

        // Increment access log
        $passportToken->increment('view_count');
        $passportToken->update(['last_accessed_at' => now()]);

        $user = $passportToken->user;

        // Controlled vital-only medical info
        return response()->json([
            'success' => true,
            'emergency_profile' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'blood_group' => $user->blood_group ?: 'অনির্ধারিত',
                'emergency_contact_phone' => $user->emergency_contact_phone ?: $user->phone,
                'emergency_medical_notes' => $user->emergency_medical_notes ?: 'কোনো বিশেষ জরুরি অ্যালার্জি বা পূর্ব রোগের তথ্য লিপিবদ্ধ নেই।',
                'avatar' => $user->avatar ? ('/'.ltrim($user->avatar, '/')) : '/images/team/default-patient.svg',
                'card_verified' => true,
                'last_verified' => now()->format('d M, Y'),
            ],
            'privacy_notice' => 'এই তথ্য শুধুমাত্র জরুরি চিকিৎসার ক্ষেত্রে জীবন রক্ষার্থে প্রদর্শিত হচ্ছে। সম্পূর্ণ প্রেসক্রিপশন বা রিপোর্ট রোগীর অনুমতি ছাড়া দৃশ্যমান নয়।',
        ]);
    }

    /**
     * Update emergency profile information.
     */
    public function updateEmergencyInfo(int $userId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        if (! $authUser || (! $isAdmin && $authUser->id !== $userId)) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $user = User::findOrFail($userId);

        $data = $request->validate([
            'blood_group' => 'nullable|string|max:5',
            'emergency_contact_phone' => 'nullable|string|max:25',
            'emergency_medical_notes' => 'nullable|string|max:500',
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'জরুরি স্বাস্থ্য তথ্য সফলভাবে আপডেট করা হয়েছে!',
            'user' => $user,
        ]);
    }
}
