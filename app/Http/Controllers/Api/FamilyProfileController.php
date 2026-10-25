<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyProfileController extends Controller
{
    /**
     * Resolve effective user ID considering role and authorization.
     */
    protected function resolveUserId(Request $request): int
    {
        $authUser = $request->user();
        if ($authUser && in_array($authUser->role, ['admin', 'super_admin'], true) && $request->filled('user_id')) {
            return (int) $request->input('user_id');
        }
        if ($authUser) {
            return (int) $authUser->id;
        }

        return (int) $request->input('user_id');
    }

    /**
     * Get all family members for the patient.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $user = User::findOrFail($userId);

        $familyMembers = FamilyMember::where('user_id', $userId)
            ->withCount(['appointments', 'healthMetrics'])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'family_members' => $familyMembers,
            'user' => $user,
        ]);
    }

    /**
     * Add a new family member (Father, Mother, Child, Spouse, etc.).
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $data = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'relationship' => 'required|in:Self,Father,Mother,Spouse,Child,Sibling,Other',
            'gender' => 'required|in:Male,Female,Other',
            'age_or_dob' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'medical_notes' => 'nullable|string|max:1000',
        ]);

        $data['user_id'] = $userId;
        $member = FamilyMember::create($data);

        return response()->json([
            'success' => true,
            'message' => "পরিবারের সদস্য '{$member->name}' সফলভাবে যুক্ত হয়েছে!",
            'family_member' => $member,
        ], 201);
    }

    /**
     * Update family member details.
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $member = FamilyMember::findOrFail($id);
        $isAdmin = $request->user() && in_array($request->user()->role, ['admin', 'super_admin'], true);
        if ($request->user() && ! $isAdmin && $member->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'relationship' => 'required|in:Self,Father,Mother,Spouse,Child,Sibling,Other',
            'gender' => 'required|in:Male,Female,Other',
            'age_or_dob' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'medical_notes' => 'nullable|string|max:1000',
        ]);

        $member->update($data);

        return response()->json([
            'success' => true,
            'message' => 'সদস্যের তথ্য আপডেট হয়েছে।',
            'family_member' => $member,
        ]);
    }

    /**
     * Delete family member.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $member = FamilyMember::findOrFail($id);
        $isAdmin = $request->user() && in_array($request->user()->role, ['admin', 'super_admin'], true);
        if ($request->user() && ! $isAdmin && $member->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'সদস্যকে রিমুভ করা হয়েছে।',
        ]);
    }
}
