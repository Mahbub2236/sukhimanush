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
        Schema::create('doctor_chambers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('chamber_name');
            $table->string('branch_name')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('upazila')->nullable();
            $table->string('phone')->nullable();
            $table->json('visiting_days')->nullable();
            $table->time('start_time')->default('16:00:00');
            $table->time('end_time')->default('21:00:00');
            $table->unsignedInteger('slot_duration_minutes')->default(20);
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'is_active']);
            $table->index(['district', 'upazila']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('chamber_id')->nullable()->after('doctor_id')->constrained('doctor_chambers')->nullOnDelete();
        });

        // Seed existing doctor chamber_address into primary chambers
        DB::table('doctors')->select([
            'id', 'name', 'chamber_address', 'city', 'district', 'upazila',
            'phone', 'available_days', 'start_time', 'end_time',
            'slot_duration_minutes', 'consultation_fee',
        ])->chunkById(200, function ($doctors) {
            $records = [];
            $now = now();
            foreach ($doctors as $doc) {
                $address = trim($doc->chamber_address ?: ($doc->city ?: 'Hospital Chamber'));
                // Extract possible chamber name or default
                $chamberName = explode(',', $address)[0] ?? 'Main Chamber';
                if (mb_strlen($chamberName) > 100) {
                    $chamberName = mb_substr($chamberName, 0, 100);
                }

                $records[] = [
                    'doctor_id' => $doc->id,
                    'chamber_name' => $chamberName,
                    'branch_name' => $doc->upazila ?: $doc->district,
                    'address' => $address,
                    'city' => $doc->city,
                    'district' => $doc->district,
                    'upazila' => $doc->upazila,
                    'phone' => $doc->phone,
                    'visiting_days' => $doc->available_days,
                    'start_time' => $doc->start_time ?: '17:00:00',
                    'end_time' => $doc->end_time ?: '21:00:00',
                    'slot_duration_minutes' => $doc->slot_duration_minutes ?: 20,
                    'consultation_fee' => $doc->consultation_fee,
                    'is_primary' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (! empty($records)) {
                DB::table('doctor_chambers')->insert($records);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['chamber_id']);
            $table->dropColumn('chamber_id');
        });

        Schema::dropIfExists('doctor_chambers');
    }
};
