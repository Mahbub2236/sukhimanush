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
        // 1. Upgrade Doctors Table with BMDC, 64-Districts Geo Data, Verification Status, Emergency Availability
        Schema::table('doctors', function (Blueprint $table) {
            if (! Schema::hasColumn('doctors', 'bmdc_number')) {
                $table->string('bmdc_number')->nullable()->unique()->after('slug');
            }
            if (! Schema::hasColumn('doctors', 'division')) {
                $table->string('division')->default('Dhaka')->after('city');
            }
            if (! Schema::hasColumn('doctors', 'district')) {
                $table->string('district')->default('Dhaka')->after('division');
            }
            if (! Schema::hasColumn('doctors', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_approved');
            }
            if (! Schema::hasColumn('doctors', 'is_emergency_available')) {
                $table->boolean('is_emergency_available')->default(false)->after('is_featured');
            }
            if (! Schema::hasColumn('doctors', 'telemedicine_fee')) {
                $table->decimal('telemedicine_fee', 8, 2)->default(50.00)->after('consultation_fee');
            }
        });

        // 2. Upgrade Appointments Table with Consultation Type, Family Member Link, Live Queue Status
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'consultation_type')) {
                $table->enum('consultation_type', ['in_chamber', 'online_video'])->default('in_chamber')->after('appointment_time');
            }
            if (! Schema::hasColumn('appointments', 'family_member_id')) {
                $table->unsignedBigInteger('family_member_id')->nullable()->after('patient_id');
            }
            if (! Schema::hasColumn('appointments', 'district')) {
                $table->string('district')->nullable()->after('consultation_type');
            }
            if (! Schema::hasColumn('appointments', 'is_called')) {
                $table->boolean('is_called')->default(false)->after('status');
            }
            if (! Schema::hasColumn('appointments', 'current_serving_serial')) {
                $table->unsignedInteger('current_serving_serial')->default(1)->after('serial_number');
            }
        });

        // 3. Family Health Profiles (Family Members Vault)
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->enum('relationship', ['Self', 'Father', 'Mother', 'Spouse', 'Child', 'Sibling', 'Other'])->default('Other');
            $table->string('gender')->default('Male');
            $table->string('age_or_dob')->nullable(); // e.g. "58 Yrs" or "2018-05-12"
            $table->string('blood_group')->nullable(); // e.g. "B+", "O+", "A+"
            $table->string('phone')->nullable();
            $table->text('medical_notes')->nullable(); // allergies, chronic illnesses
            $table->timestamps();
        });

        // 4. Chronic Disease & Vital Health Metrics (Diabetes, BP, Weight, Pulse Graph)
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('family_member_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->enum('metric_type', ['blood_sugar', 'blood_pressure', 'weight', 'pulse', 'temperature'])->default('blood_sugar');
            $table->decimal('value_primary', 8, 2); // e.g. 6.8 mmol/L for Sugar, or 120 for Systolic BP, or 68.5 for Weight
            $table->decimal('value_secondary', 8, 2)->nullable(); // e.g. 80 for Diastolic BP
            $table->string('measured_time')->nullable(); // fasting, 2h_after_meal, bedtime, random
            $table->date('recorded_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Diagnostic Center Home Sample Collection & Lab Test Orders
        Schema::create('diagnostic_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
            $table->string('diagnostic_center'); // e.g. Popular Diagnostic, Ibn Sina, Labaid, District Lab
            $table->json('test_names'); // ['CBC', 'Lipid Profile', 'Serum Creatinine']
            $table->decimal('total_amount', 8, 2)->default(0.00);
            $table->decimal('platform_commission', 8, 2)->default(0.00);
            $table->enum('sample_collection_type', ['home_collection', 'lab_visit'])->default('home_collection');
            $table->text('collection_address')->nullable();
            $table->date('preferred_date');
            $table->string('preferred_time')->default('Morning (8:00 AM - 11:00 AM)');
            $table->string('contact_phone');
            $table->enum('status', ['pending', 'confirmed', 'sample_collected', 'report_ready', 'completed', 'cancelled'])->default('pending');
            $table->string('report_file')->nullable();
            $table->timestamps();
        });

        // 6. Doctor Assistants / Compounder Sub-Accounts
        Schema::create('doctor_assistants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('access_pin', 6)->default('123456');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_assistants');
        Schema::dropIfExists('diagnostic_orders');
        Schema::dropIfExists('health_metrics');
        Schema::dropIfExists('family_members');

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['consultation_type', 'family_member_id', 'district', 'is_called', 'current_serving_serial']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['bmdc_number', 'division', 'district', 'is_verified', 'is_emergency_available', 'telemedicine_fee']);
        });
    }
};
