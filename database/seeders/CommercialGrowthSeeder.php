<?php

namespace Database\Seeders;

use App\Models\DiagnosticCenter;
use App\Models\Doctor;
use App\Models\Medicine;
use Illuminate\Database\Seeder;

class CommercialGrowthSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Bangladesh Partner Diagnostic Centers with Dynamic Commission & Test Rates
        $centers = [
            [
                'name' => 'পপুলার ডায়াগনস্টিক সেন্টার (Popular Diagnostic Center)',
                'slug' => 'popular-diagnostic-center',
                'license_number' => 'DGHS-DIAG-00412',
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila' => 'Dhanmondi',
                'address' => 'House #16, Road #2, Dhanmondi R/A, Dhaka-1205',
                'contact_phone' => '09613787801',
                'contact_email' => 'info@populardiagnostic.com',
                'commission_rate' => 15.00, // 15% Dynamic Commission
                'discount_rate' => 12.00,   // 12% Patient Discount
                'home_collection_available' => true,
                'home_collection_fee' => 120.00,
                'available_tests' => [
                    ['name' => 'Complete Blood Count (CBC)', 'price' => 450.00, 'category' => 'Hematology'],
                    ['name' => 'Fasting Blood Sugar (FBS)', 'price' => 150.00, 'category' => 'Biochemistry'],
                    ['name' => 'HbA1c (Glycated Hemoglobin)', 'price' => 950.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Lipid Profile', 'price' => 1100.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Creatinine', 'price' => 400.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Electrolytes', 'price' => 900.00, 'category' => 'Biochemistry'],
                    ['name' => 'SGPT / ALT (Liver Function)', 'price' => 400.00, 'category' => 'Biochemistry'],
                    ['name' => 'Thyroid Stimulating Hormone (TSH)', 'price' => 650.00, 'category' => 'Immunology'],
                    ['name' => 'Urine Routine Examination (R/E)', 'price' => 250.00, 'category' => 'Clinical Pathology'],
                    ['name' => 'Ultrasonography of Whole Abdomen (USG)', 'price' => 1400.00, 'category' => 'Radiology'],
                    ['name' => 'Chest X-Ray P/A View', 'price' => 650.00, 'category' => 'Radiology'],
                    ['name' => '12-Lead Digital ECG', 'price' => 450.00, 'category' => 'Cardiology'],
                ],
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'name' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার (Ibn Sina Diagnostic)',
                'slug' => 'ibn-sina-diagnostic-center',
                'license_number' => 'DGHS-DIAG-00874',
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila' => 'Mirpur',
                'address' => 'House #48, Road #9/A, Dhanmondi / Mirpur-10, Dhaka',
                'contact_phone' => '09610010615',
                'contact_email' => 'service@ibnsinatrust.com',
                'commission_rate' => 18.00, // 18% Dynamic Commission
                'discount_rate' => 10.00,   // 10% Patient Discount
                'home_collection_available' => true,
                'home_collection_fee' => 100.00,
                'available_tests' => [
                    ['name' => 'Complete Blood Count (CBC)', 'price' => 400.00, 'category' => 'Hematology'],
                    ['name' => 'Fasting Blood Sugar (FBS)', 'price' => 130.00, 'category' => 'Biochemistry'],
                    ['name' => 'HbA1c', 'price' => 900.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Lipid Profile', 'price' => 1000.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Creatinine', 'price' => 380.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Uric Acid', 'price' => 400.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Bilirubin (Total & Direct)', 'price' => 400.00, 'category' => 'Biochemistry'],
                    ['name' => 'Vitamin D (25-OH)', 'price' => 2200.00, 'category' => 'Immunology'],
                    ['name' => 'Urine R/E & C/S', 'price' => 650.00, 'category' => 'Microbiology'],
                    ['name' => 'Ultrasonography of Whole Abdomen (USG)', 'price' => 1300.00, 'category' => 'Radiology'],
                    ['name' => 'Chest X-Ray P/A View', 'price' => 600.00, 'category' => 'Radiology'],
                ],
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'name' => 'ল্যাবএইড ডায়াগনস্টিক (Labaid Diagnostic Center)',
                'slug' => 'labaid-diagnostic-center',
                'license_number' => 'DGHS-DIAG-00219',
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila' => 'Gulshan',
                'address' => 'House #1, Road #4, Dhanmondi & Gulshan-2, Dhaka',
                'contact_phone' => '10606',
                'contact_email' => 'info@labaidgroup.com',
                'commission_rate' => 20.00, // 20% Dynamic Commission
                'discount_rate' => 15.00,   // 15% Patient Discount
                'home_collection_available' => true,
                'home_collection_fee' => 150.00,
                'available_tests' => [
                    ['name' => 'Complete Blood Count (CBC)', 'price' => 500.00, 'category' => 'Hematology'],
                    ['name' => 'Fasting Blood Sugar (FBS)', 'price' => 180.00, 'category' => 'Biochemistry'],
                    ['name' => 'HbA1c', 'price' => 1050.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Lipid Profile', 'price' => 1250.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Creatinine', 'price' => 450.00, 'category' => 'Biochemistry'],
                    ['name' => 'Serum Electrolytes', 'price' => 950.00, 'category' => 'Biochemistry'],
                    ['name' => 'Echocardiogram 2D & Color Doppler', 'price' => 2200.00, 'category' => 'Cardiology'],
                    ['name' => '12-Lead Digital ECG', 'price' => 500.00, 'category' => 'Cardiology'],
                    ['name' => 'Chest X-Ray P/A View', 'price' => 700.00, 'category' => 'Radiology'],
                    ['name' => 'Ultrasonography of Whole Abdomen (USG)', 'price' => 1600.00, 'category' => 'Radiology'],
                ],
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'name' => 'প্রাভা হেলথ ডায়াগনস্টিক (Praava Health Lab & Diagnostics)',
                'slug' => 'praava-health-diagnostics',
                'license_number' => 'DGHS-DIAG-01124',
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila' => 'Banani',
                'address' => 'Plot 9, Road 17, Block C, Banani, Dhaka-1213',
                'contact_phone' => '10648',
                'contact_email' => 'care@praavahealth.com',
                'commission_rate' => 20.00, // 20% Dynamic Commission
                'discount_rate' => 15.00,   // 15% Patient Discount
                'home_collection_available' => true,
                'home_collection_fee' => 150.00,
                'available_tests' => [
                    ['name' => 'Complete Blood Count (CBC)', 'price' => 550.00, 'category' => 'Hematology'],
                    ['name' => 'Fasting Blood Sugar (FBS)', 'price' => 200.00, 'category' => 'Biochemistry'],
                    ['name' => 'Comprehensive Diabetes Panel', 'price' => 1800.00, 'category' => 'Biochemistry'],
                    ['name' => 'Comprehensive Heart Health Panel', 'price' => 2400.00, 'category' => 'Biochemistry'],
                    ['name' => 'Thyroid Profile (T3, T4, TSH)', 'price' => 1400.00, 'category' => 'Immunology'],
                    ['name' => 'Vitamin D (25-OH)', 'price' => 2400.00, 'category' => 'Immunology'],
                    ['name' => 'Home Sample Collection Full Package', 'price' => 3200.00, 'category' => 'Health Checkup'],
                ],
                'is_approved' => true,
                'is_active' => true,
            ],
        ];

        foreach ($centers as $center) {
            DiagnosticCenter::updateOrCreate(
                ['slug' => $center['slug']],
                $center
            );
        }

        // 2. Seed Common Bangladesh Medicines with Brand, Generic, Company & Realistic Unit Price (MRP)
        $medicines = [
            // Paracetamol / Pain
            ['brand_name' => 'Napa 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 1.20],
            ['brand_name' => 'Napa Extra', 'generic_name' => 'Paracetamol + Caffeine', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 3.00],
            ['brand_name' => 'Napa One 1000mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 2.50],
            ['brand_name' => 'Napa Syrup', 'generic_name' => 'Paracetamol (120mg/5ml)', 'dosage_form' => 'Syrup', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 35.00],
            ['brand_name' => 'Ace 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 1.20],
            ['brand_name' => 'Ace Plus', 'generic_name' => 'Paracetamol + Caffeine', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 3.00],
            ['brand_name' => 'Renova 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'company' => 'Renata Limited', 'unit_price' => 1.20],
            ['brand_name' => 'Fast 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'company' => 'Acme Laboratories Ltd.', 'unit_price' => 1.20],

            // Gastric / Acid Suppressants (PPI)
            ['brand_name' => 'Seclo 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 6.00],
            ['brand_name' => 'Seclo 40mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 10.00],
            ['brand_name' => 'Sergel 20mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'company' => 'Healthcare Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Sergel 40mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'company' => 'Healthcare Pharmaceuticals Ltd.', 'unit_price' => 12.00],
            ['brand_name' => 'Nexum 20mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Pantonix 20mg', 'generic_name' => 'Pantoprazole', 'dosage_form' => 'Tab', 'company' => 'Incepta Pharmaceuticals Ltd.', 'unit_price' => 7.00],
            ['brand_name' => 'Pantonix 40mg', 'generic_name' => 'Pantoprazole', 'dosage_form' => 'Tab', 'company' => 'Incepta Pharmaceuticals Ltd.', 'unit_price' => 11.00],
            ['brand_name' => 'Losectil 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'company' => 'Eskayef Pharmaceuticals Ltd. (SK-F)', 'unit_price' => 6.00],
            ['brand_name' => 'Finix 20mg', 'generic_name' => 'Rabeprazole', 'dosage_form' => 'Tab', 'company' => 'Opsonin Pharma Ltd.', 'unit_price' => 7.00],
            ['brand_name' => 'Entacyd Plus', 'generic_name' => 'Magaldrate + Simethicone', 'dosage_form' => 'Syrup', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 95.00],

            // Antibiotics
            ['brand_name' => 'Azith 500mg', 'generic_name' => 'Azithromycin', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 35.00],
            ['brand_name' => 'Zithrin 500mg', 'generic_name' => 'Azithromycin', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 35.00],
            ['brand_name' => 'Ciprocin 500mg', 'generic_name' => 'Ciprofloxacin', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 15.00],
            ['brand_name' => 'Neoflox 500mg', 'generic_name' => 'Ciprofloxacin', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 15.00],
            ['brand_name' => 'Moxaclav 625mg', 'generic_name' => 'Amoxicillin + Clavulanic Acid', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 32.00],
            ['brand_name' => 'Cef-3 200mg', 'generic_name' => 'Cefixime', 'dosage_form' => 'Cap', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 45.00],
            ['brand_name' => 'Triocim 200mg', 'generic_name' => 'Cefixime', 'dosage_form' => 'Cap', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 45.00],

            // Allergy, Cold & Cough
            ['brand_name' => 'Alatrol 10mg', 'generic_name' => 'Cetirizine', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 3.50],
            ['brand_name' => 'Fexo 120mg', 'generic_name' => 'Fexofenadine', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 9.00],
            ['brand_name' => 'Fexo 180mg', 'generic_name' => 'Fexofenadine', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 12.00],
            ['brand_name' => 'Telfast 120mg', 'generic_name' => 'Fexofenadine', 'dosage_form' => 'Tab', 'company' => 'Sanofi Bangladesh Ltd.', 'unit_price' => 11.00],
            ['brand_name' => 'Monas 10mg', 'generic_name' => 'Montelukast', 'dosage_form' => 'Tab', 'company' => 'Acme Laboratories Ltd.', 'unit_price' => 17.50],
            ['brand_name' => 'Montene 10mg', 'generic_name' => 'Montelukast', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 17.50],
            ['brand_name' => 'Tofen 1mg', 'generic_name' => 'Ketotifen', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 2.50],
            ['brand_name' => 'Tusca Syrup', 'generic_name' => 'Dextromethorphan + Pseudoephedrine', 'dosage_form' => 'Syrup', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 90.00],

            // Pain & Anti-inflammatory (NSAID)
            ['brand_name' => 'Flexi 100mg', 'generic_name' => 'Aceclofenac', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 5.50],
            ['brand_name' => 'Aceclo 100mg', 'generic_name' => 'Aceclofenac', 'dosage_form' => 'Tab', 'company' => 'Incepta Pharmaceuticals Ltd.', 'unit_price' => 5.50],
            ['brand_name' => 'Naprosyn 500mg', 'generic_name' => 'Naproxen', 'dosage_form' => 'Tab', 'company' => 'Radiant Pharmaceuticals Ltd.', 'unit_price' => 10.00],
            ['brand_name' => 'Rolac 10mg', 'generic_name' => 'Ketorolac Tromethamine', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 12.00],

            // Diabetes & Hypertension
            ['brand_name' => 'Comet 500mg', 'generic_name' => 'Metformin HCl', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 4.50],
            ['brand_name' => 'Comet 850mg', 'generic_name' => 'Metformin HCl', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 6.50],
            ['brand_name' => 'Gluconor 500mg', 'generic_name' => 'Metformin HCl', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 4.50],
            ['brand_name' => 'Bislol 2.5mg', 'generic_name' => 'Bisoprolol Fumarate', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 6.00],
            ['brand_name' => 'Bizoran 5/20', 'generic_name' => 'Amlodipine + Olmesartan', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 12.00],
            ['brand_name' => 'Angilock 50mg', 'generic_name' => 'Losartan Potassium', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Osartil 50mg', 'generic_name' => 'Losartan Potassium', 'dosage_form' => 'Tab', 'company' => 'Incepta Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Nebita 5mg', 'generic_name' => 'Nebivolol', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Lipicon 10mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tab', 'company' => 'Eskayef Pharmaceuticals Ltd.', 'unit_price' => 12.00],

            // Vitamins & Minerals
            ['brand_name' => 'Calbo-D', 'generic_name' => 'Calcium Carbonate + Vitamin D3', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 7.00],
            ['brand_name' => 'Coralcal-D', 'generic_name' => 'Calcium Orotate + Vitamin D3', 'dosage_form' => 'Tab', 'company' => 'Radiant Pharmaceuticals Ltd.', 'unit_price' => 14.00],
            ['brand_name' => 'Filwel Gold', 'generic_name' => 'Multivitamin & Multimineral (A-Z)', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 5.00],
            ['brand_name' => 'Bextram Gold', 'generic_name' => 'Multivitamin & Multimineral', 'dosage_form' => 'Tab', 'company' => 'Beximco Pharmaceuticals Ltd.', 'unit_price' => 5.00],
            ['brand_name' => 'Neuro-B', 'generic_name' => 'Vitamin B1 + B6 + B12', 'dosage_form' => 'Tab', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Omega-3 1000mg', 'generic_name' => 'Omega-3 Fish Oil', 'dosage_form' => 'Cap', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 15.00],
        ];

        foreach ($medicines as $med) {
            Medicine::updateOrCreate(
                ['brand_name' => $med['brand_name']],
                $med
            );
        }

        // 3. Upgrade top 2 featured doctors to PRO / VIP Membership Plan with Gold Badges
        $topDoctors = Doctor::take(3)->get();
        if ($topDoctors->isNotEmpty()) {
            if (isset($topDoctors[0])) {
                $topDoctors[0]->update([
                    'membership_plan' => 'vip',
                    'is_featured' => true,
                    'membership_expires_at' => now()->addYear(),
                ]);
            }
            if (isset($topDoctors[1])) {
                $topDoctors[1]->update([
                    'membership_plan' => 'pro',
                    'is_featured' => true,
                    'membership_expires_at' => now()->addMonths(6),
                ]);
            }
        }
    }
}
