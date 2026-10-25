<?php

namespace App\Http\Middleware;

use App\Models\DoctorAssistant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request and ensure the authenticated user has the required role.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাক্সেস! অনুগ্রহ করে লগইন করুন। (Unauthenticated)',
            ], 401);
        }

        // Determine the role of the authenticated entity
        $userRole = null;
        if ($user instanceof DoctorAssistant) {
            $userRole = 'staff';
        } elseif (isset($user->role)) {
            $userRole = $user->role;
        }

        // Super Admin & Admin have universal administrative access
        if (in_array($userRole, ['admin', 'super_admin'], true)) {
            return $next($request);
        }

        // Check if user's role matches any of the allowed roles
        if (! in_array($userRole, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'এই অ্যাকশনটি সম্পন্ন করার অনুমতি আপনার নেই। (Forbidden: Unauthorized Role)',
            ], 403);
        }

        // Ensure Doctor and Agent accounts are verified and approved by admin
        if (in_array($userRole, ['doctor', 'agent'], true) && isset($user->is_approved) && ! $user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'আপনার অ্যাকাউন্টটি বর্তমানে অ্যাডমিনের অনুমোদনের অপেক্ষায় রয়েছে (Pending Approval)। অনুমোদনের পর আপনি পোর্টালে কাজ করতে পারবেন।',
            ], 403);
        }

        return $next($request);
    }
}
