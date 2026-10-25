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
        Schema::create('patient_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique()->index(); // e.g. REP-2026-89124
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('report_title'); // e.g. Complete Blood Count (CBC), Chest X-Ray
            $table->string('test_type')->default('blood_test'); // blood_test, xray, usg, ecg, urine, mri_ct, pathology, prescription_copy, other
            $table->string('diagnostic_center')->nullable(); // e.g. Popular Diagnostic, Ibn Sina, Sadar Hospital
            $table->date('report_date')->nullable();
            $table->string('file_path'); // stored file relative path
            $table->string('file_type')->default('image'); // image, pdf
            $table->string('file_size')->nullable(); // e.g. 1.5 MB
            $table->text('patient_notes')->nullable();
            $table->text('doctor_remarks')->nullable();
            $table->enum('status', ['uploaded', 'reviewed', 'urgent'])->default('uploaded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_reports');
    }
};
