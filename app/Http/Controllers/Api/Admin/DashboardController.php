<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Get aggregate SaaS super admin metrics & revenue.
     */
    public function stats(): JsonResponse
    {
        $today = now()->format('Y-m-d');

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $adminCommission = Payment::where('status', 'completed')->sum('admin_commission');
        $doctorPayouts = Payment::where('status', 'completed')->sum('doctor_earning');

        $stats = [
            'total_revenue' => round($totalRevenue, 2),
            'admin_commission' => round($adminCommission, 2),
            'doctor_payouts' => round($doctorPayouts, 2),
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'total_doctors' => Doctor::count(),
            'pending_doctors' => Doctor::where('is_approved', false)->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_agents' => User::where('role', 'agent')->count(),
            'pending_agents' => User::where('role', 'agent')->where('is_approved', false)->count(),
            'total_staff' => DoctorAssistant::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get all appointments across all doctors.
     */
    public function appointments(Request $request): JsonResponse
    {
        $query = Appointment::with(['doctor', 'patient', 'prescription'])->orderByDesc('created_at');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('appointment_number', 'like', "%{$s}%")
                    ->orWhere('patient_name', 'like', "%{$s}%")
                    ->orWhere('patient_phone', 'like', "%{$s}%");
            });
        }

        $appointments = $query->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Get all doctors roster with search, filter & pagination.
     */
    public function doctors(Request $request): JsonResponse
    {
        $query = Doctor::withCount('appointments')
            ->with(['user'])
            ->orderBy('name');

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('specialization', 'like', "%{$s}%")
                    ->orWhere('designation', 'like', "%{$s}%")
                    ->orWhere('chamber_address', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%")
                    ->orWhere('upazila', 'like', "%{$s}%")
                    ->orWhere('bmdc_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            if ($request->input('status') === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->input('status') === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('verified') && $request->input('verified') !== 'all') {
            if ($request->input('verified') === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->input('verified') === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 15;
        }

        $doctors = $query->paginate($perPage);

        return response()->json($doctors);
    }

    /**
     * Get single doctor details for viewing or editing.
     */
    public function showDoctor(int $id): JsonResponse
    {
        $doctor = Doctor::with(['user'])->findOrFail($id);

        return response()->json($doctor);
    }

    /**
     * Update doctor profile details from Admin Panel.
     */
    public function updateDoctor(int $id, Request $request): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'qualifications' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:2000',
            'chamber_address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'district' => 'nullable|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'consultation_fee' => 'nullable|numeric|min:0',
            'telemedicine_fee' => 'nullable|numeric|min:0',
            'bmdc_number' => 'nullable|string|max:100',
            'start_time' => 'nullable|string|max:50',
            'end_time' => 'nullable|string|max:50',
            'slot_duration_minutes' => 'nullable|integer|min:5|max:120',
            'available_days' => 'nullable|array',
            'is_approved' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_emergency_available' => 'nullable|boolean',
        ]);

        $doctor->update($validated);

        return response()->json([
            'success' => true,
            'message' => "ডাঃ {$doctor->name}-এর তথ্য সফলভাবে আপডেট করা হয়েছে।",
            'doctor' => $doctor->fresh(),
        ]);
    }

    /**
     * Delete a doctor from the admin panel.
     */
    public function destroyDoctor(int $id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        $name = $doctor->name;
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => "ডাক্তার {$name}-কে সফলভাবে অপসারণ করা হয়েছে।",
        ]);
    }

    /**
     * Toggle doctor approval or featured status.
     */
    public function toggleDoctorStatus(int $id, Request $request): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);

        if ($request->has('is_approved')) {
            $doctor->is_approved = (bool) $request->input('is_approved');
        }

        if ($request->has('is_verified')) {
            $doctor->is_verified = (bool) $request->input('is_verified');
        }

        if ($request->has('is_featured')) {
            $doctor->is_featured = (bool) $request->input('is_featured');
        }

        if ($request->has('is_emergency_available')) {
            $doctor->is_emergency_available = (bool) $request->input('is_emergency_available');
        }

        if ($request->has('membership_plan')) {
            $doctor->membership_plan = $request->input('membership_plan');
            if (in_array($doctor->membership_plan, ['pro', 'vip'], true)) {
                $doctor->is_featured = true;
                $doctor->membership_expires_at = now()->addMonths((int) $request->input('duration_months', 12));
            } else {
                $doctor->membership_expires_at = null;
            }
        }

        $doctor->save();

        return response()->json([
            'success' => true,
            'message' => "Doctor {$doctor->name} status updated.",
            'doctor' => $doctor,
        ]);
    }

    /**
     * Admin: Update Doctor SaaS Membership Plan (Free, Pro, VIP).
     */
    public function updateDoctorMembership(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'membership_plan' => 'required|in:free,pro,vip',
            'duration_months' => 'nullable|integer',
        ]);

        $doctor = Doctor::findOrFail($id);
        $plan = $request->input('membership_plan');
        $doctor->membership_plan = $plan;
        $doctor->is_featured = in_array($plan, ['pro', 'vip'], true);

        $months = (int) $request->input('duration_months', 12);
        $doctor->membership_expires_at = $plan === 'free' ? null : now()->addMonths($months);
        $doctor->save();

        Cache::forget("doctor_profile_{$doctor->slug}");
        Cache::forget("doctor_profile_{$doctor->id}");

        return response()->json([
            'success' => true,
            'message' => "ডাক্তার {$doctor->name}-এর মেম্বারশিপ প্ল্যান সফলভাবে ".strtoupper($plan).'-এ আপডেট করা হয়েছে!',
            'doctor' => $doctor,
        ]);
    }

    /**
     * Get all financial payment transactions.
     */
    public function payments(): JsonResponse
    {
        $payments = Payment::with(['doctor', 'patient', 'appointment'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($payments);
    }

    /**
     * Update appointment status by Super Admin.
     */
    public function updateAppointmentStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $appointment = Appointment::with('doctor')->findOrFail($id);
        $appointment->status = $request->input('status');
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => "Appointment {$appointment->appointment_number} status updated to {$appointment->status}.",
            'appointment' => $appointment,
        ]);
    }

    /**
     * Contact inquiries.
     */
    public function messages(): JsonResponse
    {
        $messages = ContactMessage::orderByDesc('created_at')->paginate(20);

        return response()->json($messages);
    }

    public function markMessageRead(int $id): JsonResponse
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Agents (Rural Digital Health Points) Management
    |--------------------------------------------------------------------------
    */
    public function agents(Request $request): JsonResponse
    {
        $query = User::where('role', 'agent')
            ->withCount(['agentAppointments as total_bookings'])
            ->withSum('agentAppointments as total_commission_earned', 'agent_commission')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('pharmacy_name', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            if ($request->input('status') === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->input('status') === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $agents = $query->paginate(20);

        return response()->json($agents);
    }

    public function storeAgent(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'pharmacy_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'agent_commission_balance' => 'nullable|numeric|min:0',
        ]);

        $agent = User::create([
            'name' => $data['name'],
            'pharmacy_name' => $data['pharmacy_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'agent',
            'is_approved' => true, // Manually created by Admin
            'address' => $data['address'] ?? null,
            'agent_commission_balance' => $data['agent_commission_balance'] ?? 0.00,
        ]);

        return response()->json([
            'success' => true,
            'message' => "এজেন্ট {$agent->pharmacy_name} সফলভাবে যুক্ত হয়েছে।",
            'agent' => $agent,
        ], 201);
    }

    public function updateAgentWallet(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => 'required|in:add,deduct,set',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $agent = User::where('role', 'agent')->findOrFail($id);

        if ($data['action'] === 'add') {
            $agent->increment('agent_commission_balance', $data['amount']);
        } elseif ($data['action'] === 'deduct') {
            $agent->agent_commission_balance = max(0, (float) $agent->agent_commission_balance - $data['amount']);
            $agent->save();
        } else {
            $agent->agent_commission_balance = $data['amount'];
            $agent->save();
        }

        return response()->json([
            'success' => true,
            'message' => "এজেন্ট ওয়ালেট সফলভাবে আপডেট করা হয়েছে। নতুন ব্যালেন্স: ৳{$agent->agent_commission_balance}",
            'agent' => $agent,
        ]);
    }

    public function toggleAgentStatus(int $id, Request $request): JsonResponse
    {
        $agent = User::where('role', 'agent')->findOrFail($id);

        if ($request->has('is_approved')) {
            $agent->is_approved = (bool) $request->input('is_approved');
        } else {
            $agent->is_approved = ! $agent->is_approved;
        }
        $agent->save();

        $statusText = $agent->is_approved ? 'অনুমোদিত (Approved)' : 'স্থগিত (Disabled)';

        return response()->json([
            'success' => true,
            'message' => "এজেন্ট '{$agent->pharmacy_name}' এর স্ট্যাটাস সফলভাবে {$statusText} করা হয়েছে।",
            'agent' => $agent,
        ]);
    }

    public function destroyAgent(int $id): JsonResponse
    {
        $agent = User::where('role', 'agent')->findOrFail($id);
        $agentName = $agent->pharmacy_name ?: $agent->name;
        $agent->delete();

        return response()->json([
            'success' => true,
            'message' => "এজেন্ট {$agentName} মুছে ফেলা হয়েছে।",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Staff (Chamber Compounders / Assistants) Management
    |--------------------------------------------------------------------------
    */
    public function staffList(Request $request): JsonResponse
    {
        $query = DoctorAssistant::with('doctor')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhereHas('doctor', function ($dq) use ($s) {
                        $dq->where('name', 'like', "%{$s}%")
                            ->orWhere('chamber_address', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('doctor_id') && $request->input('doctor_id') !== 'all') {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $staff = $query->paginate(20);

        return response()->json($staff);
    }

    public function storeStaff(Request $request): JsonResponse
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'access_pin' => 'required|string|min:4|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        $assistant = DoctorAssistant::create([
            'doctor_id' => $data['doctor_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? ($data['phone'].'@staff.sukhimanush.local'),
            'access_pin' => $data['access_pin'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "চেম্বার সহকারী {$assistant->name} সফলভাবে যুক্ত হয়েছে।",
            'staff' => $assistant->load('doctor'),
        ], 201);
    }

    public function toggleStaffStatus(int $id): JsonResponse
    {
        $assistant = DoctorAssistant::findOrFail($id);
        $assistant->is_active = ! $assistant->is_active;
        $assistant->save();

        $statusText = $assistant->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়';

        return response()->json([
            'success' => true,
            'message' => "সহকারী {$assistant->name}-এর স্ট্যাটাস {$statusText} করা হয়েছে।",
            'staff' => $assistant,
        ]);
    }

    public function resetStaffPin(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'access_pin' => 'required|string|min:4|max:10',
        ]);

        $assistant = DoctorAssistant::findOrFail($id);
        $assistant->access_pin = $data['access_pin'];
        $assistant->save();

        return response()->json([
            'success' => true,
            'message' => "সহকারী {$assistant->name}-এর নতুন অ্যাক্সেস পিন সফলভাবে সংরক্ষিত হয়েছে।",
            'staff' => $assistant,
        ]);
    }

    public function destroyStaff(int $id): JsonResponse
    {
        $assistant = DoctorAssistant::findOrFail($id);
        $name = $assistant->name;
        $assistant->delete();

        return response()->json([
            'success' => true,
            'message' => "সহকারী {$name} মুছে ফেলা হয়েছে।",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Patients Management
    |--------------------------------------------------------------------------
    */
    public function patients(Request $request): JsonResponse
    {
        $query = User::where('role', 'patient')
            ->withCount(['appointments as total_appointments', 'prescriptions as total_prescriptions', 'familyMembers as total_family_members'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $patients = $query->paginate(20);

        return response()->json($patients);
    }

    public function destroyPatient(int $id): JsonResponse
    {
        $patient = User::where('role', 'patient')->findOrFail($id);
        $name = $patient->name;
        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => "রোগীর অ্যাকাউন্ট {$name} মুছে ফেলা হয়েছে।",
        ]);
    }
}
