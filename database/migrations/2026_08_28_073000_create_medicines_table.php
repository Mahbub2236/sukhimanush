<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->index();
            $table->string('generic_name')->index();
            $table->string('dosage_form')->default('Tab'); // Tab, Cap, Syrup, Drop, Inj, Ointment
            $table->string('strength')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
