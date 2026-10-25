<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Live Chamber Queue Sessions (Per Doctor Per Date)
        if (! Schema::hasTable('chamber_queues')) {
            Schema::create('chamber_queues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->date('queue_date')->index();
                $table->enum('doctor_status', ['not_arrived', 'on_the_way', 'consultation_started', 'paused', 'completed'])->default('not_arrived');
                $table->unsignedInteger('current_token')->default(0);
                $table->unsignedInteger('total_tokens')->default(0);
                $table->unsignedInteger('completed_tokens')->default(0);
                $table->unsignedInteger('avg_minutes_per_patient')->default(12);
                $table->string('notice_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('last_called_at')->nullable();
                $table->timestamps();

                $table->unique(['doctor_id', 'queue_date']);
            });
        }

        // 2. Daily Medicine Schedules & Adherence Logs
        if (! Schema::hasTable('medicine_schedules')) {
            Schema::create('medicine_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('family_member_id')->nullable()->constrained('family_members')->nullOnDelete();
                $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
                $table->string('medicine_name');
                $table->string('dosage')->default('1টি');
                $table->enum('time_slot', ['morning', 'noon', 'evening', 'night'])->default('morning');
                $table->enum('meal_instruction', ['after_meal', 'before_meal', 'with_meal', 'anytime'])->default('after_meal');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->enum('status', ['active', 'paused', 'completed'])->default('active');
                $table->json('adherence_logs')->nullable();
                $table->timestamps();
            });
        }

        // 3. Emergency Blood & Platelet SOS Requests
        if (! Schema::hasTable('blood_requests')) {
            Schema::create('blood_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_code')->unique()->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('patient_name');
                $table->string('blood_group', 5);
                $table->enum('component', ['whole_blood', 'platelets', 'plasma', 'rbc'])->default('whole_blood');
                $table->unsignedInteger('units_needed')->default(1);
                $table->string('hospital_name');
                $table->string('district')->default('Dhaka');
                $table->string('upazila')->nullable();
                $table->string('contact_name');
                $table->string('contact_phone');
                $table->enum('urgency', ['critical_immediate', 'within_12h', 'within_24h', 'scheduled'])->default('within_24h');
                $table->text('reason')->nullable();
                $table->enum('status', ['open', 'matched', 'fulfilled', 'cancelled'])->default('open');
                $table->timestamps();
            });
        }

        // 4. Voluntary Blood Donors Registry
        if (! Schema::hasTable('blood_donors')) {
            Schema::create('blood_donors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('blood_group', 5);
                $table->string('district');
                $table->string('upazila')->nullable();
                $table->string('phone');
                $table->date('last_donated_date')->nullable();
                $table->boolean('is_available')->default(true);
                $table->unsignedInteger('total_donations')->default(0);
                $table->string('badge')->default('bronze');
                $table->timestamps();
            });
        }

        // 5. Medical Cost Estimator & Transparent Hospital Benchmarks
        if (! Schema::hasTable('procedure_costs')) {
            Schema::create('procedure_costs', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_bn');
                $table->string('category');
                $table->decimal('min_cost', 10, 2);
                $table->decimal('max_cost', 10, 2);
                $table->json('cost_breakdown')->nullable();
                $table->unsignedInteger('typical_stay_days')->default(1);
                $table->text('included_services_bn')->nullable();
                $table->text('excluded_services_bn')->nullable();
                $table->string('verification_badge')->default('Verified Market Range');
                $table->timestamps();
            });
        }

        // 6. Hospital ICU / CCU / NICU Bed Availability
        if (! Schema::hasTable('hospital_beds')) {
            Schema::create('hospital_beds', function (Blueprint $table) {
                $table->id();
                $table->string('hospital_name');
                $table->string('hospital_type')->default('Private');
                $table->string('district');
                $table->string('upazila')->nullable();
                $table->string('hotline');
                $table->string('emergency_phone')->nullable();
                $table->unsignedInteger('icu_available')->default(0);
                $table->unsignedInteger('icu_total')->default(10);
                $table->unsignedInteger('ccu_available')->default(0);
                $table->unsignedInteger('nicu_available')->default(0);
                $table->unsignedInteger('general_beds_available')->default(0);
                $table->timestamp('last_verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 7. Verified Ambulance Providers & Emergency Transport
        if (! Schema::hasTable('ambulance_providers')) {
            Schema::create('ambulance_providers', function (Blueprint $table) {
                $table->id();
                $table->string('service_name');
                $table->string('driver_or_contact_name');
                $table->string('phone');
                $table->enum('vehicle_type', ['ac', 'non_ac', 'icu_support', 'freezer'])->default('ac');
                $table->string('district');
                $table->string('upazila')->nullable();
                $table->decimal('base_fare', 8, 2)->default(1000.00);
                $table->decimal('per_km_rate', 8, 2)->default(30.00);
                $table->boolean('is_available_24_7')->default(true);
                $table->timestamps();
            });
        }

        // 8. Controlled-Access Emergency Health Passport Tokens
        if (! Schema::hasTable('emergency_passport_tokens')) {
            Schema::create('emergency_passport_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('token', 64)->unique()->index();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamps();
            });
        }

        // 9. Upgrade Users table for Agent / Pharmacy & Emergency Contacts
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pharmacy_name')) {
                $table->string('pharmacy_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_medical_notes')) {
                $table->text('emergency_medical_notes')->nullable();
            }
            if (! Schema::hasColumn('users', 'agent_commission_balance')) {
                $table->decimal('agent_commission_balance', 10, 2)->default(0.00);
            }
        });

        // 10. Upgrade Appointments table for Agent attribution
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('patient_id');
            }
            if (! Schema::hasColumn('appointments', 'agent_commission')) {
                $table->decimal('agent_commission', 8, 2)->default(0.00)->after('consultation_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['agent_id', 'agent_commission']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pharmacy_name', 'emergency_contact_phone', 'emergency_medical_notes', 'agent_commission_balance']);
        });

        Schema::dropIfExists('emergency_passport_tokens');
        Schema::dropIfExists('ambulance_providers');
        Schema::dropIfExists('hospital_beds');
        Schema::dropIfExists('procedure_costs');
        Schema::dropIfExists('blood_donors');
        Schema::dropIfExists('blood_requests');
        Schema::dropIfExists('medicine_schedules');
        Schema::dropIfExists('chamber_queues');
    }
};
