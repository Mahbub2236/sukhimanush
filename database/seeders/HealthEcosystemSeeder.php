<?php

namespace Database\Seeders;

use App\Models\AmbulanceProvider;
use App\Models\BloodDonor;
use App\Models\HospitalBed;
use App\Models\ProcedureCost;
use App\Models\User;
use Illuminate\Database\Seeder;

class HealthEcosystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Default Pharmacy Agent / Rural Digital Health Point User
        User::firstOrCreate(
            ['email' => 'agent@sukhimanush.local'],
            [
                'name' => 'মোঃ রফিকুল ইসলাম (সততা ফার্মেসি)',
                'pharmacy_name' => 'মেসার্স সততা ডিজিটাল হেলথ পয়েন্ট',
                'phone' => '01711998877',
                'password' => bcrypt('password'),
                'role' => 'agent',
                'agent_commission_balance' => 450.00,
            ]
        );

        // 2. Realistic Medical Procedure & Surgery Cost Benchmarks
        $procedures = [
            [
                'name_en' => 'Caesarean Delivery (C-Section)',
                'name_bn' => 'সিজারিয়ান ডেলিভারি (সি-সেকশন)',
                'category' => 'সার্জারি (Surgery)',
                'min_cost' => 25000.00,
                'max_cost' => 55000.00,
                'cost_breakdown' => [
                    'সার্জন ও এনেস্থেসিয়া ফি' => 18000,
                    'অপারেশন থিয়েটার (OT) চার্জ' => 12000,
                    'কেবিন / বেড ভাড়া (৩ দিন)' => 9000,
                    'মেডিসিন ও স্যালাইন' => 7000,
                ],
                'typical_stay_days' => 3,
                'included_services_bn' => 'সার্জন ফি, এনেস্থেসিয়া, ৩ দিনের পোস্ট-অপারেটিভ কেয়ার, ওটি চার্জ',
                'excluded_services_bn' => 'এনআইসিইউ (NICU) প্রয়োজন হলে অতিরিক্ত চার্জ, স্পেশাল রক্তদান',
            ],
            [
                'name_en' => 'Normal Delivery Package',
                'name_bn' => 'স্বাভাবিক প্রসব (নরমাল ডেলিভারি)',
                'category' => 'সার্জারি (Surgery)',
                'min_cost' => 10000.00,
                'max_cost' => 25000.00,
                'cost_breakdown' => [
                    'গাইনী কনসালটেন্ট ফি' => 6000,
                    'লেবার রুম ও ডেলিভারি চার্জ' => 5000,
                    'বেড চার্জ (১ দিন)' => 3000,
                    'মেডিসিন ও নবজাতক চেকআপ' => 2500,
                ],
                'typical_stay_days' => 1,
                'included_services_bn' => 'ডাক্তার ফি, শিশু বিশেষজ্ঞের প্রথম চেকআপ, বেড ভাড়া',
                'excluded_services_bn' => 'জরুরি সিজারে রূপান্তর হলে অতিরিক্ত প্যাকেজ চার্জ',
            ],
            [
                'name_en' => 'Appendectomy (Laparoscopic / Open)',
                'name_bn' => 'অ্যাপেন্ডিসেক্টমি (অ্যাপেন্ডিক্স অপারেশন)',
                'category' => 'সার্জারি (Surgery)',
                'min_cost' => 22000.00,
                'max_cost' => 45000.00,
                'cost_breakdown' => [
                    'সার্জন ফি' => 14000,
                    'ল্যাপারোস্কোপিক ওটি চার্জ' => 11000,
                    'কেবিন চার্জ (২ দিন)' => 6000,
                    'অ্যান্টিবায়োটিক ও ওষুধ' => 4000,
                ],
                'typical_stay_days' => 2,
                'included_services_bn' => 'ল্যাপারোস্কোপিক সার্জারি, এনেস্থেসিয়া, ২ দিনের ভর্তি',
                'excluded_services_bn' => 'বায়োপসি / হিস্টোপ্যাথলজি টেস্ট চার্জ',
            ],
            [
                'name_en' => 'Laparoscopic Cholecystectomy (Gallbladder Stone)',
                'name_bn' => 'পিত্তথলির পাথর অপারেশন (ল্যাপারোস্কোপি)',
                'category' => 'সার্জারি (Surgery)',
                'min_cost' => 28000.00,
                'max_cost' => 60000.00,
                'cost_breakdown' => [
                    'বিশেষজ্ঞ সার্জন ফি' => 20000,
                    'মডার্ন ওটি চার্জ' => 14000,
                    'পোস্ট ওটি কেয়ার ও বেড (২ দিন)' => 8000,
                    'মেডিসিন ও ডিসপোজেবল' => 6000,
                ],
                'typical_stay_days' => 2,
                'included_services_bn' => '৩টি ছোট ছিদ্রের ল্যাপারোস্কোপিক সার্জারি, দ্রুত রিকভারি',
                'excluded_services_bn' => 'সিবিডি জটিলতা দেখা দিলে অতিরিক্ত ইআরসিপি চার্জ',
            ],
            [
                'name_en' => 'Echocardiogram (Color Doppler Echo)',
                'name_bn' => 'ইকোকার্ডিওগ্রাম (কালার ডপলার ইকো)',
                'category' => 'ডায়াগনস্টিক টেস্ট',
                'min_cost' => 2000.00,
                'max_cost' => 4500.00,
                'cost_breakdown' => [
                    'কার্ডিওলজিস্ট ইন্টারপ্রিটেশন' => 1500,
                    'হাই-এন্ড মেশিন চার্জ' => 1200,
                ],
                'typical_stay_days' => 0,
                'included_services_bn' => 'কালার ডপলার ইমেজ, হার্ট ইজেকশন ফ্র্যাকশন (EF%) বিশ্লেষণ ও বিশেষজ্ঞ রিপোর্ট',
                'excluded_services_bn' => 'অন্যান্য রক্ত পরীক্ষা',
            ],
            [
                'name_en' => 'MRI Brain / Spine (1.5 Tesla)',
                'name_bn' => 'এমআরআই - ব্রেন অথবা স্পাইন (১.৫ টেসলা)',
                'category' => 'ডায়াগনস্টিক টেস্ট',
                'min_cost' => 7000.00,
                'max_cost' => 12000.00,
                'cost_breakdown' => [
                    'রেডিওলজিস্ট রিপোর্ট' => 3000,
                    'স্ক্যানিং ও কন্ট্রাস্ট' => 5000,
                ],
                'typical_stay_days' => 0,
                'included_services_bn' => 'হাই-রেজোলিউশন ফিল্ম, ডিজিটাল সিডি ড্রাইভ, কনসালটেন্ট রেডিওলজিস্ট সাইন',
                'excluded_services_bn' => 'কন্ট্রাস্ট ডাই প্রয়োজন হলে অতিরিক্ত ৳২,০০০',
            ],
            [
                'name_en' => 'Endoscopy of Upper GI Tract',
                'name_bn' => 'এন্ডোস্কোপি (গ্যাস্ট্রিক ও আলসার পরীক্ষা)',
                'category' => 'ডায়াগনস্টিক টেস্ট',
                'min_cost' => 2500.00,
                'max_cost' => 5000.00,
                'cost_breakdown' => [
                    'গ্যাস্ট্রোএন্টারোলজিস্ট ফি' => 1800,
                    'স্কোপ মেশিন ও ক্যামেরা' => 1200,
                ],
                'typical_stay_days' => 0,
                'included_services_bn' => 'গলা অবশ করার স্প্রে, কালার ডিজিটাল ইমেজ প্রিন্ট ও রিপোর্ট',
                'excluded_services_bn' => 'এইচ পাইলরি বা বায়োপসি নমুনা কালচার ফি',
            ],
        ];

        foreach ($procedures as $proc) {
            ProcedureCost::updateOrCreate(['name_en' => $proc['name_en']], $proc);
        }

        // 3. Hospital ICU / CCU Bed Availability Radar
        $hospitals = [
            [
                'hospital_name' => 'ঢাকা মেডিকেল কলেজ হাসপাতাল (DMCH)',
                'hospital_type' => 'সরকারি (Govt)',
                'district' => 'Dhaka',
                'upazila' => 'Shahbagh',
                'hotline' => '02-55165088',
                'emergency_phone' => '01711000001',
                'icu_available' => 3,
                'icu_total' => 30,
                'ccu_available' => 2,
                'nicu_available' => 4,
                'general_beds_available' => 15,
                'last_verified_at' => now()->subMinutes(15),
            ],
            [
                'hospital_name' => 'পপুলার মেডিকেল কলেজ হাসপাতাল',
                'hospital_type' => 'বেসরকারি (Private)',
                'district' => 'Dhaka',
                'upazila' => 'Dhanmondi',
                'hotline' => '09613787801',
                'emergency_phone' => '01713000002',
                'icu_available' => 5,
                'icu_total' => 20,
                'ccu_available' => 4,
                'nicu_available' => 6,
                'general_beds_available' => 28,
                'last_verified_at' => now()->subMinutes(8),
            ],
            [
                'hospital_name' => 'স্কয়ার হাসপাতাল ঢাকা',
                'hospital_type' => 'বেসরকারি (Private)',
                'district' => 'Dhaka',
                'upazila' => 'Panthapath',
                'hotline' => '10616',
                'emergency_phone' => '01713377775',
                'icu_available' => 4,
                'icu_total' => 35,
                'ccu_available' => 3,
                'nicu_available' => 5,
                'general_beds_available' => 40,
                'last_verified_at' => now()->subMinutes(25),
            ],
            [
                'hospital_name' => 'রংপুর কমিউনিটি মেডিকেল কলেজ হাসপাতাল',
                'hospital_type' => 'বেসরকারি (Private)',
                'district' => 'Rangpur',
                'upazila' => 'Rangpur Sadar',
                'hotline' => '0521-61111',
                'emergency_phone' => '01712888888',
                'icu_available' => 4,
                'icu_total' => 16,
                'ccu_available' => 2,
                'nicu_available' => 3,
                'general_beds_available' => 35,
                'last_verified_at' => now()->subMinutes(12),
            ],
            [
                'hospital_name' => 'সিলেট এম এ জি ওসমানী মেডিকেল কলেজ',
                'hospital_type' => 'সরকারি (Govt)',
                'district' => 'Sylhet',
                'upazila' => 'Sylhet Sadar',
                'hotline' => '0821-713667',
                'emergency_phone' => '01715444333',
                'icu_available' => 2,
                'icu_total' => 25,
                'ccu_available' => 1,
                'nicu_available' => 3,
                'general_beds_available' => 20,
                'last_verified_at' => now()->subMinutes(40),
            ],
        ];

        foreach ($hospitals as $hosp) {
            HospitalBed::updateOrCreate(['hospital_name' => $hosp['hospital_name']], $hosp);
        }

        // 4. Voluntary Blood Donors
        $donors = [
            ['name' => 'মোঃ তানভীর আহমেদ', 'blood_group' => 'O+', 'district' => 'Dhaka', 'upazila' => 'Dhanmondi', 'phone' => '01711223344', 'total_donations' => 14, 'badge' => 'gold'],
            ['name' => 'ফারহানা ইয়াসমিন', 'blood_group' => 'A+', 'district' => 'Dhaka', 'upazila' => 'Mirpur', 'phone' => '01812334455', 'total_donations' => 8, 'badge' => 'silver'],
            ['name' => 'মাহমুদুল হাসান', 'blood_group' => 'B+', 'district' => 'Rangpur', 'upazila' => 'Rangpur Sadar', 'phone' => '01719556677', 'total_donations' => 22, 'badge' => 'life_saver'],
            ['name' => 'সাদিয়া সুলতানা', 'blood_group' => 'AB+', 'district' => 'Sylhet', 'upazila' => 'Sylhet Sadar', 'phone' => '01911447788', 'total_donations' => 6, 'badge' => 'silver'],
            ['name' => 'আরিফুল হক', 'blood_group' => 'O-', 'district' => 'Dhaka', 'upazila' => 'Uttara', 'phone' => '01611889900', 'total_donations' => 18, 'badge' => 'life_saver'],
            ['name' => 'মোসাদ্দেক হোসেন', 'blood_group' => 'B-', 'district' => 'Chittagong', 'upazila' => 'Panchlaish', 'phone' => '01819776655', 'total_donations' => 11, 'badge' => 'gold'],
        ];

        foreach ($donors as $d) {
            BloodDonor::updateOrCreate(['phone' => $d['phone']], $d);
        }

        // 5. Verified Ambulance Providers
        $ambulances = [
            ['service_name' => 'ঢাকা লাইফ লাইন আইসিইউ এম্বুলেন্স', 'driver_or_contact_name' => 'মোঃ সোহেল রানা', 'phone' => '01711550011', 'vehicle_type' => 'icu_support', 'district' => 'Dhaka', 'upazila' => 'Dhanmondi', 'base_fare' => 3500.00, 'per_km_rate' => 50.00],
            ['service_name' => 'গ্রিন ভিউ এসি এম্বুলেন্স সার্ভিস', 'driver_or_contact_name' => 'মোঃ আল-আমিন', 'phone' => '01811442233', 'vehicle_type' => 'ac', 'district' => 'Dhaka', 'upazila' => 'Mohakhali', 'base_fare' => 1500.00, 'per_km_rate' => 35.00],
            ['service_name' => 'রংপুর এক্সপ্রেস জরুরি এম্বুলেন্স', 'driver_or_contact_name' => 'মোঃ মজিবুর রহমান', 'phone' => '01712667788', 'vehicle_type' => 'ac', 'district' => 'Rangpur', 'upazila' => 'Rangpur Sadar', 'base_fare' => 1200.00, 'per_km_rate' => 30.00],
            ['service_name' => 'সুরক্ষা ফ্রিজার এম্বুলেন্স', 'driver_or_contact_name' => 'মোঃ কামাল হোসেন', 'phone' => '01911993344', 'vehicle_type' => 'freezer', 'district' => 'Dhaka', 'upazila' => 'Mirpur', 'base_fare' => 2500.00, 'per_km_rate' => 45.00],
        ];

        foreach ($ambulances as $amb) {
            AmbulanceProvider::updateOrCreate(['phone' => $amb['phone']], $amb);
        }
    }
}
