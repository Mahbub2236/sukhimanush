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
        Schema::table('appointments', function (Blueprint $table) {
            // Composite index for ultra-fast slot availability check & concurrency lookup
            $table->index(['doctor_id', 'appointment_date', 'appointment_time', 'status'], 'idx_doc_date_time_status');
            $table->index(['doctor_id', 'appointment_date', 'serial_number'], 'idx_doc_date_serial');
            $table->index(['patient_phone'], 'idx_patient_phone');
        });

        Schema::table('doctors', function (Blueprint $table) {
            // Composite indexes for multi-filter doctor search & listing
            $table->index(['is_approved', 'is_featured', 'is_verified'], 'idx_approved_featured_verified');
            $table->index(['is_approved', 'free_consultation_enabled'], 'idx_approved_free_consult');
            $table->index(['is_approved', 'division', 'district'], 'idx_approved_division_district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_doc_date_time_status');
            $table->dropIndex('idx_doc_date_serial');
            $table->dropIndex('idx_patient_phone');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex('idx_approved_featured_verified');
            $table->dropIndex('idx_approved_free_consult');
            $table->dropIndex('idx_approved_division_district');
        });
    }
};
