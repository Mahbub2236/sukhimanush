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
            if (! Schema::hasColumn('appointments', 'consultation_type')) {
                $table->string('consultation_type')->default('in_chamber')->after('consultation_fee');
            }
            if (! Schema::hasColumn('appointments', 'meeting_link')) {
                $table->string('meeting_link', 500)->nullable()->after('consultation_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['consultation_type', 'meeting_link']);
        });
    }
};
