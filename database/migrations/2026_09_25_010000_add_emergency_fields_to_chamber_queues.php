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
        Schema::table('chamber_queues', function (Blueprint $table) {
            $table->boolean('is_emergency_active')->default(false)->after('doctor_status');
            $table->string('emergency_reason')->nullable()->after('is_emergency_active');
            $table->string('emergency_patient_name')->nullable()->after('emergency_reason');
            $table->timestamp('emergency_started_at')->nullable()->after('emergency_patient_name');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('is_called');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('is_emergency');
        });

        Schema::table('chamber_queues', function (Blueprint $table) {
            $table->dropColumn([
                'is_emergency_active',
                'emergency_reason',
                'emergency_patient_name',
                'emergency_started_at',
            ]);
        });
    }
};
