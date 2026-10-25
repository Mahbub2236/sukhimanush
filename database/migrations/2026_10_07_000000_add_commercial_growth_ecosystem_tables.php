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
        // 1. Diagnostic Centers Table with Dynamic Commission and Available Tests
        if (! Schema::hasTable('diagnostic_centers')) {
            Schema::create('diagnostic_centers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('license_number')->nullable();
                $table->string('division')->default('Dhaka');
                $table->string('district')->default('Dhaka');
                $table->string('upazila')->nullable();
                $table->text('address');
                $table->string('contact_phone');
                $table->string('contact_email')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(15.00); // Dynamic Platform Commission % (e.g. 15.00%)
                $table->decimal('discount_rate', 5, 2)->default(10.00); // Patient Discount % (e.g. 10.00%)
                $table->boolean('home_collection_available')->default(true);
                $table->decimal('home_collection_fee', 8, 2)->default(100.00);
                $table->json('available_tests')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Link diagnostic_orders with diagnostic_centers & snapshot dynamic commission
        Schema::table('diagnostic_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('diagnostic_orders', 'diagnostic_center_id')) {
                $table->foreignId('diagnostic_center_id')->nullable()->after('prescription_id')->constrained('diagnostic_centers')->nullOnDelete();
            }
            if (! Schema::hasColumn('diagnostic_orders', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(15.00)->after('total_amount');
            }
            if (! Schema::hasColumn('diagnostic_orders', 'patient_discount')) {
                $table->decimal('patient_discount', 8, 2)->default(0.00)->after('commission_rate');
            }
        });

        // 3. Upgrade Doctors table with Membership Plan (Free, Pro, VIP) and Expiry
        Schema::table('doctors', function (Blueprint $table) {
            if (! Schema::hasColumn('doctors', 'membership_plan')) {
                $table->string('membership_plan')->default('free')->after('is_featured'); // 'free', 'pro', 'vip'
            }
            if (! Schema::hasColumn('doctors', 'membership_expires_at')) {
                $table->timestamp('membership_expires_at')->nullable()->after('membership_plan');
            }
        });

        // 4. Upgrade Medicines table with unit retail price (MRP)
        Schema::table('medicines', function (Blueprint $table) {
            if (! Schema::hasColumn('medicines', 'unit_price')) {
                $table->decimal('unit_price', 8, 2)->default(0.00)->after('company');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            if (Schema::hasColumn('medicines', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'membership_plan')) {
                $table->dropColumn(['membership_plan', 'membership_expires_at']);
            }
        });

        Schema::table('diagnostic_orders', function (Blueprint $table) {
            if (Schema::hasColumn('diagnostic_orders', 'diagnostic_center_id')) {
                $table->dropConstrainedForeignId('diagnostic_center_id');
            }
            if (Schema::hasColumn('diagnostic_orders', 'commission_rate')) {
                $table->dropColumn(['commission_rate', 'patient_discount']);
            }
        });

        Schema::dropIfExists('diagnostic_centers');
    }
};
