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
            $table->index(['doctor_id', 'appointment_date', 'status'], 'idx_doc_date_status');
            $table->index(['patient_id', 'status'], 'idx_patient_status');
        });

        Schema::table('doctor_chambers', function (Blueprint $table) {
            $table->index(['doctor_id', 'is_primary'], 'idx_doc_chamber_primary');
        });

        Schema::table('patient_reports', function (Blueprint $table) {
            $table->index(['patient_id', 'created_at'], 'idx_patient_reports_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_doc_date_status');
            $table->dropIndex('idx_patient_status');
        });

        Schema::table('doctor_chambers', function (Blueprint $table) {
            $table->dropIndex('idx_doc_chamber_primary');
        });

        Schema::table('patient_reports', function (Blueprint $table) {
            $table->dropIndex('idx_patient_reports_created');
        });
    }
};
