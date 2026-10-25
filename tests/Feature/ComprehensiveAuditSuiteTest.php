<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComprehensiveAuditSuiteTest extends TestCase
{
    // Use DatabaseTransactions so tests rollback cleanly without dropping tables
    use DatabaseTransactions;

    /**
     * 1. Public API Endpoints Tests
     */
    public function test_public_home_data_endpoint_returns_success(): void
    {
        $response = $this->getJson('/api/home-data');
        $response->assertStatus(200)
            ->assertJsonStructure(['featured_doctors', 'specializations', 'testimonials', 'recent_blogs', 'stats']);
    }

    public function test_public_doctors_listing_returns_valid_structure(): void
    {
        $response = $this->getJson('/api/doctors');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'doctors',
                'specializations',
            ]);
    }

    public function test_public_legal_policies_returns_sukhi_manush_terms(): void
    {
        $response = $this->getJson('/api/policies');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_public_emergency_overview_endpoint(): void
    {
        $response = $this->getJson('/api/emergency/overview');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cost_estimator_procedures_listing(): void
    {
        $response = $this->getJson('/api/cost-estimator/procedures');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * 2. Authentication Flow & Validation
     */
    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent_user@example.com',
            'password' => 'wrong_password_123',
        ]);
        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_successful_login_returns_token_and_user_profile(): void
    {
        $user = User::create([
            'name' => 'QA Test Patient',
            'email' => 'qa_test_patient_'.time().'@sukhimanush.com',
            'password' => Hash::make('secret123'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['token', 'user']);
    }

    /**
     * 3. Role-Based Access Control (RBAC) & Middleware Protection
     */
    public function test_unauthenticated_request_to_patient_portal_is_rejected(): void
    {
        $response = $this->getJson('/api/patient-portal/dashboard');
        $response->assertStatus(401);
    }

    public function test_patient_cannot_access_super_admin_stats(): void
    {
        $patient = User::create([
            'name' => 'Regular Patient',
            'email' => 'reg_patient_'.time().'@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($patient, 'sanctum')->getJson('/api/admin/stats');
        $response->assertStatus(403);
    }

    /**
     * 4. IDOR & Data Isolation Authorization Tests (User A cannot access User B's info)
     */
    public function test_user_a_cannot_access_user_b_health_passport_token(): void
    {
        $userA = User::create([
            'name' => 'User Alice',
            'email' => 'alice_'.time().'@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        $userB = User::create([
            'name' => 'User Bob',
            'email' => 'bob_'.time().'@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        // Alice tries to access Bob's private passport token
        $response = $this->actingAs($userA, 'sanctum')
            ->getJson("/api/health-passport/token/{$userB->id}");

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_user_a_cannot_update_user_b_emergency_info(): void
    {
        $userA = User::create([
            'name' => 'Attacker Alice',
            'email' => 'alice_attacker_'.time().'@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        $userB = User::create([
            'name' => 'Victim Bob',
            'email' => 'bob_victim_'.time().'@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($userA, 'sanctum')
            ->postJson("/api/health-passport/emergency-info/{$userB->id}", [
                'blood_group' => 'AB+',
                'emergency_medical_notes' => 'Tampered notes',
            ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    /**
     * 5. Appointment Booking Validation
     */
    public function test_appointment_booking_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/appointments', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['doctor_id', 'patient_name', 'patient_phone', 'appointment_date', 'appointment_time']);
    }

    /**
     * 6. Payment Webhook Mocking (bKash IPN)
     */
    public function test_bkash_webhook_rejects_empty_payload(): void
    {
        config(['services.bkash.secret' => 'qa-secret']);
        $response = $this->withHeaders(['X-Webhook-Secret' => 'qa-secret'])
            ->postJson('/api/payments/webhook/bkash', []);
        $response->assertStatus(400);
    }
}
