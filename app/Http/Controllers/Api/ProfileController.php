<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Upload and update user or doctor profile avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'user_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
        ]);

        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);

        // Determine authorized user ID: non-admins can only update their own avatar
        $targetUserId = ($isAdmin && $request->filled('user_id'))
            ? (int) $request->input('user_id')
            : ($authUser ? $authUser->id : null);

        if (! $targetUserId && ! $isAdmin) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $file = $request->file('avatar');
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($file->getMimeType(), $allowedMimes, true)) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত ছবির ফরম্যাট। (Invalid image MIME type)'], 422);
        }

        $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
        $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        if (! in_array($ext, $allowedExtensions, true)) {
            $ext = 'jpg';
        }

        $filename = 'avatar_'.time().'_'.uniqid().'.'.$ext;
        $file->storeAs('avatars', $filename, 'public');

        $avatarPath = 'storage/avatars/'.$filename;
        $updatedUser = null;
        $updatedDoctor = null;

        // 1. Update Doctor Profile if doctor_id provided
        if ($request->filled('doctor_id')) {
            $doctor = Doctor::find($request->doctor_id);
            // Verify ownership: must be admin or doctor linked to auth user
            if ($doctor && ($isAdmin || ($authUser && $doctor->user_id === $authUser->id))) {
                if ($doctor->avatar && str_starts_with($doctor->avatar, 'storage/avatars/')) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $doctor->avatar));
                }
                $doctor->avatar = $avatarPath;
                $doctor->save();
                $updatedDoctor = $doctor;

                if ($doctor->user_id) {
                    $user = User::find($doctor->user_id);
                    if ($user) {
                        $user->avatar = $avatarPath;
                        $user->save();
                        $updatedUser = $user;
                    }
                }
            }
        }

        // 2. Update User Profile
        if ($targetUserId && ! $updatedUser) {
            $user = User::find($targetUserId);
            if ($user) {
                if ($user->avatar && str_starts_with($user->avatar, 'storage/avatars/')) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar));
                }
                $user->avatar = $avatarPath;
                $user->save();
                $updatedUser = $user;

                if ($user->role === 'doctor') {
                    $doc = Doctor::where('user_id', $user->id)->first();
                    if ($doc) {
                        $doc->avatar = $avatarPath;
                        $doc->save();
                        $updatedDoctor = $doc;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
            'avatar_url' => '/'.$avatarPath,
            'user' => $updatedUser,
            'doctor' => $updatedDoctor,
        ]);
    }

    /**
     * Update user details (Name, Phone, Address, Password).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = in_array($authUser->role, ['admin', 'super_admin'], true);

        // Security check: Non-admins cannot update any user profile other than their own
        if ($request->filled('user_id') && (int) $request->input('user_id') !== $authUser->id && ! $isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের প্রোফাইল আপডেট করতে পারেন। (Forbidden: Unauthorized user modification)',
            ], 403);
        }

        $targetUserId = ($isAdmin && $request->filled('user_id'))
            ? (int) $request->input('user_id')
            : $authUser->id;

        $user = User::findOrFail($targetUserId);
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
            // Revoke all existing tokens to invalidate compromised or stale sessions
            $user->tokens()->delete();
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile information updated successfully!',
            'user' => $user,
        ]);
    }
}
