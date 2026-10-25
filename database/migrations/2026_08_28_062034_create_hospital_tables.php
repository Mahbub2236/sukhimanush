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
        // 1. Modify or recreate Users table with SaaS roles
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('services');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super_admin', 'doctor', 'patient'])->default('patient');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Doctors Table (SaaS independent doctor profiles with chamber address & schedule)
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('specialization'); // e.g. Cardiology, Pediatrics, Neurology, Dermatology, Orthopedics, Dental Care
            $table->string('designation'); // e.g. Senior Consultant Cardiologist
            $table->string('qualifications')->nullable(); // e.g. MBBS, FCPS, MD
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('chamber_address'); // Doctor's own clinic/chamber address
            $table->string('city')->default('New York');
            $table->decimal('consultation_fee', 8, 2)->default(50.00);
            $table->unsignedInteger('experience_years')->default(5);
            $table->json('available_days')->nullable(); // ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('17:00:00');
            $table->unsignedInteger('slot_duration_minutes')->default(30);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_featured')->default(true);
            $table->timestamps();
        });

        // 3. Appointments Table
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique()->index();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('patient_phone');
            $table->date('appointment_date');
            $table->string('appointment_time');
            $table->unsignedInteger('serial_number')->default(1);
            $table->decimal('consultation_fee', 8, 2)->default(50.00);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('paid');
            $table->string('payment_method')->default('online_card'); // online_card, bkash, cash
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->text('reason_for_visit')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
        });

        // 4. Prescriptions Table (Doctor writes digital prescription for patient)
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique()->index(); // e.g. RX-2026-9812
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('patient_name');
            $table->string('patient_age_gender')->nullable(); // e.g. 32 Yrs / Male
            $table->text('diagnosis')->nullable(); // Symptoms / Clinical diagnosis
            $table->json('medicines')->nullable(); // [{ name: 'Napa Extra 500mg', dosage: '1+0+1', duration: '5 days', instruction: 'After Meal' }]
            $table->json('medical_tests')->nullable(); // ['CBC', 'X-Ray Chest', 'ECG']
            $table->text('advice')->nullable(); // Doctor general instructions
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 5. Payments / SaaS Commission Transactions
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 8, 2);
            $table->decimal('admin_commission', 8, 2)->default(5.00); // 10% platform fee
            $table->decimal('doctor_earning', 8, 2);
            $table->string('payment_method')->default('card');
            $table->enum('status', ['completed', 'pending', 'refunded'])->default('completed');
            $table->timestamps();
        });

        // 6. Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 7. Blog Posts
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Health Tips');
            $table->string('author_name')->default('Medical Team');
            $table->string('image')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 8. Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('title')->nullable();
            $table->text('review');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('users');
    }
};
