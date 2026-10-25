<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) NOT NULL DEFAULT 'patient'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('patient')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'doctor', 'patient', 'staff') NOT NULL DEFAULT 'patient'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('patient')->change();
            });
        }
    }
};
