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
        Schema::create('legal_policies', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'privacy_policy', 'terms_of_service', 'telemedicine_consent'
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->json('sections')->nullable(); // structured sections for rich interactive UI
            $table->string('effective_date')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_policies');
    }
};
