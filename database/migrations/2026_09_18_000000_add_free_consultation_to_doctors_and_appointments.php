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
        Schema::table('doctors', function (Blueprint $table) {
            $table->boolean('free_consultation_enabled')->default(false)->after('is_emergency_available');
            $table->json('free_days')->nullable()->after('free_consultation_enabled');
            $table->string('free_start_time')->nullable()->after('free_days');
            $table->string('free_end_time')->nullable()->after('free_start_time');
            $table->integer('free_patient_limit')->default(20)->after('free_end_time');
            $table->text('free_chamber_address')->nullable()->after('free_patient_limit');
            $table->text('free_service_note')->nullable()->after('free_chamber_address');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_free_consultation')->default(false)->after('consultation_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn([
                'free_consultation_enabled',
                'free_days',
                'free_start_time',
                'free_end_time',
                'free_patient_limit',
                'free_chamber_address',
                'free_service_note',
            ]);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('is_free_consultation');
        });
    }
};
