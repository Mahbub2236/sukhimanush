<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

        // Security: Ensure only authorized administrators can access this portal
        if (! in_array($user->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাক্সেস! শুধুমাত্র অ্যাডমিন অ্যাকাউন্ট দিয়ে লগইন করা সম্ভব। (Forbidden: Admin role required)',
            ], 403);
        }

        // Return user info and Sanctum auth token
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }
}
