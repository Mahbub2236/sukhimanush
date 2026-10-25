<?php

namespace Database\Seeders;

use App\Models\LegalPolicy;
use Illuminate\Database\Seeder;

class LegalPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPolicies = LegalPolicy::getDefaultPolicies();

        foreach ($defaultPolicies as $policyData) {
            LegalPolicy::updateOrCreate(
                ['key' => $policyData['key']],
                $policyData
            );
        }
    }
}
