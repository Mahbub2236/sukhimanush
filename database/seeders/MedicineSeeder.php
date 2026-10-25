<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            // Fever & Pain (Paracetamol & Combinations)
            ['brand_name' => 'Napa 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Napa Extra', 'generic_name' => 'Paracetamol + Caffeine', 'dosage_form' => 'Tab', 'strength' => '500mg + 65mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Napa Extend 665mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '665mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Napa Syrup', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Syrup', 'strength' => '120mg/5ml', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Ace 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Ace Plus', 'generic_name' => 'Paracetamol + Caffeine', 'dosage_form' => 'Tab', 'strength' => '500mg + 65mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Ace XR 665mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '665mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Fast 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Acme Laboratories'],
            ['brand_name' => 'Renova 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Opsonin Pharma'],
            ['brand_name' => 'Reset Plus', 'generic_name' => 'Paracetamol + Caffeine', 'dosage_form' => 'Tab', 'strength' => '500mg + 65mg', 'company' => 'Incepta Pharma'],

            // Gastric / PPI (Acidity & Ulcer)
            ['brand_name' => 'Sergel 20mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Healthcare Pharma'],
            ['brand_name' => 'Sergel 40mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'strength' => '40mg', 'company' => 'Healthcare Pharma'],
            ['brand_name' => 'Maxpro 20mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Renata Ltd'],
            ['brand_name' => 'Maxpro 40mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'strength' => '40mg', 'company' => 'Renata Ltd'],
            ['brand_name' => 'Seclo 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Seclo 40mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'strength' => '40mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Losectil 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Eskayef (SK-F)'],
            ['brand_name' => 'Pantonix 20mg', 'generic_name' => 'Pantoprazole', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Pantonix 40mg', 'generic_name' => 'Pantoprazole', 'dosage_form' => 'Tab', 'strength' => '40mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Nexum 20mg', 'generic_name' => 'Esomeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Proceptin 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Cap', 'strength' => '20mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Finix 20mg', 'generic_name' => 'Rabeprazole', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Opsonin Pharma'],
            ['brand_name' => 'Rabeloc 20mg', 'generic_name' => 'Rabeprazole', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Gaviscon Liquid', 'generic_name' => 'Sodium Alginate + Sodium Bicarbonate', 'dosage_form' => 'Suspension', 'strength' => '200ml', 'company' => 'Reckitt Benckiser'],
            ['brand_name' => 'Marlox Plus', 'generic_name' => 'Magaldrate + Simethicone', 'dosage_form' => 'Suspension', 'strength' => '200ml', 'company' => 'Square Pharma'],
            ['brand_name' => 'Entacyd Plus', 'generic_name' => 'Magaldrate + Simethicone', 'dosage_form' => 'Suspension', 'strength' => '200ml', 'company' => 'Square Pharma'],

            // Antibiotics
            ['brand_name' => 'Zithrox 500mg', 'generic_name' => 'Azithromycin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Zimax 500mg', 'generic_name' => 'Azithromycin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Tridosil 500mg', 'generic_name' => 'Azithromycin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Ciprocin 500mg', 'generic_name' => 'Ciprofloxacin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Neofloxin 500mg', 'generic_name' => 'Ciprofloxacin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Cef-3 200mg', 'generic_name' => 'Cefixime', 'dosage_form' => 'Cap', 'strength' => '200mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Cef-3 400mg', 'generic_name' => 'Cefixime', 'dosage_form' => 'Cap', 'strength' => '400mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'T-Cef 200mg', 'generic_name' => 'Cefixime', 'dosage_form' => 'Cap', 'strength' => '200mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Moxaclav 625mg', 'generic_name' => 'Amoxicillin + Clavulanic Acid', 'dosage_form' => 'Tab', 'strength' => '625mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Fimoxyclav 625mg', 'generic_name' => 'Amoxicillin + Clavulanic Acid', 'dosage_form' => 'Tab', 'strength' => '625mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Levoking 500mg', 'generic_name' => 'Levofloxacin', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Filmet 400mg', 'generic_name' => 'Metronidazole', 'dosage_form' => 'Tab', 'strength' => '400mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Amodis 400mg', 'generic_name' => 'Metronidazole', 'dosage_form' => 'Tab', 'strength' => '400mg', 'company' => 'Beximco Pharma'],

            // Anti-Allergy & Respiratory (Antihistamines / Asthma)
            ['brand_name' => 'Fexo 120mg', 'generic_name' => 'Fexofenadine Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '120mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Fexo 180mg', 'generic_name' => 'Fexofenadine Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '180mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Fodex 120mg', 'generic_name' => 'Fexofenadine Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '120mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Alatrol 10mg', 'generic_name' => 'Cetirizine Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Atrizin 10mg', 'generic_name' => 'Cetirizine Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Bilash 20mg', 'generic_name' => 'Bilastine', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Bilaxten 20mg', 'generic_name' => 'Bilastine', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Monas 10mg', 'generic_name' => 'Montelukast', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Acme Laboratories'],
            ['brand_name' => 'Montene 10mg', 'generic_name' => 'Montelukast', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Odmon 10mg', 'generic_name' => 'Montelukast', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Rupafin 10mg', 'generic_name' => 'Rupatadine', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Tofen Syrup', 'generic_name' => 'Ketotifen', 'dosage_form' => 'Syrup', 'strength' => '1mg/5ml', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Advaspray Inhaler', 'generic_name' => 'Fluticasone + Salmeterol', 'dosage_form' => 'Inhaler', 'strength' => '125mcg/25mcg', 'company' => 'Square Pharma'],

            // Pain, NSAIDs & Muscle Relaxant
            ['brand_name' => 'Torax 10mg', 'generic_name' => 'Ketorolac Tromethamine', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Rolac 10mg', 'generic_name' => 'Ketorolac Tromethamine', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Renata Ltd'],
            ['brand_name' => 'Clofenac 50mg', 'generic_name' => 'Diclofenac Sodium', 'dosage_form' => 'Tab', 'strength' => '50mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Xelpro 200mg', 'generic_name' => 'Celecoxib', 'dosage_form' => 'Cap', 'strength' => '200mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Etorix 90mg', 'generic_name' => 'Etoricoxib', 'dosage_form' => 'Tab', 'strength' => '90mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Napryn 500mg', 'generic_name' => 'Naproxen', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Muscol 50mg', 'generic_name' => 'Tolperisone Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '50mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Myolax 50mg', 'generic_name' => 'Tolperisone Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '50mg', 'company' => 'Incepta Pharma'],

            // Cardiovascular & Blood Pressure (Cardiology)
            ['brand_name' => 'Bizoran 5/20', 'generic_name' => 'Amlodipine + Olmesartan', 'dosage_form' => 'Tab', 'strength' => '5mg + 20mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Bizoran 5/40', 'generic_name' => 'Amlodipine + Olmesartan', 'dosage_form' => 'Tab', 'strength' => '5mg + 40mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Olmetec 20mg', 'generic_name' => 'Olmesartan Medoxomil', 'dosage_form' => 'Tab', 'strength' => '20mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Camlosart 5/20', 'generic_name' => 'Amlodipine + Olmesartan', 'dosage_form' => 'Tab', 'strength' => '5mg + 20mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Nebita 5mg', 'generic_name' => 'Nebivolol', 'dosage_form' => 'Tab', 'strength' => '5mg', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 8.00],
            ['brand_name' => 'Lipicon 10mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Eskayef Pharmaceuticals Ltd.', 'unit_price' => 12.00],
            ['brand_name' => 'Bystolic 5mg', 'generic_name' => 'Nebivolol', 'dosage_form' => 'Tab', 'strength' => '5mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Lipiget 10mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Lipitor 10mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Pfizer / Radiant'],
            ['brand_name' => 'Atova 10mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Rosuva 10mg', 'generic_name' => 'Rosuvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Rostab 10mg', 'generic_name' => 'Rosuvastatin', 'dosage_form' => 'Tab', 'strength' => '10mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Ecosprin 75mg', 'generic_name' => 'Aspirin', 'dosage_form' => 'Tab', 'strength' => '75mg', 'company' => 'Acme Laboratories'],
            ['brand_name' => 'Lopirel 75mg', 'generic_name' => 'Clopidogrel', 'dosage_form' => 'Tab', 'strength' => '75mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Anclog 75mg', 'generic_name' => 'Clopidogrel', 'dosage_form' => 'Tab', 'strength' => '75mg', 'company' => 'Beximco Pharma'],

            // Vitamins & Minerals
            ['brand_name' => 'Calbo-D', 'generic_name' => 'Calcium Carbonate + Vitamin D3', 'dosage_form' => 'Tab', 'strength' => '500mg + 200IU', 'company' => 'Square Pharma'],
            ['brand_name' => 'Coralcal-D', 'generic_name' => 'Calcium Orotate + Vitamin D3', 'dosage_form' => 'Tab', 'strength' => '500mg + 200IU', 'company' => 'Radiant Pharma'],
            ['brand_name' => 'Bicozin', 'generic_name' => 'Vitamin B Complex + Zinc', 'dosage_form' => 'Tab', 'strength' => 'Multivitamin + Zinc', 'company' => 'Square Pharma'],
            ['brand_name' => 'Aristovit-B', 'generic_name' => 'Vitamin B Complex', 'dosage_form' => 'Tab', 'strength' => 'Standard', 'company' => 'Aristopharma'],
            ['brand_name' => 'Neuro-B', 'generic_name' => 'Vitamin B1 + B6 + B12', 'dosage_form' => 'Tab', 'strength' => '100mg+200mg+200mcg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Neurobion', 'generic_name' => 'Vitamin B1 + B6 + B12', 'dosage_form' => 'Tab', 'strength' => '100mg+200mg+200mcg', 'company' => 'Merck / Healthcare'],
            ['brand_name' => 'D-Rise 20000 IU', 'generic_name' => 'Cholecalciferol (Vitamin D3)', 'dosage_form' => 'Cap', 'strength' => '20000 IU', 'company' => 'Square Pharma'],
            ['brand_name' => 'D-Rise 40000 IU', 'generic_name' => 'Cholecalciferol (Vitamin D3)', 'dosage_form' => 'Cap', 'strength' => '40000 IU', 'company' => 'Square Pharma'],
            ['brand_name' => 'Surbex-Z', 'generic_name' => 'Zinc + Vitamin B-Complex + Vitamin C + E', 'dosage_form' => 'Tab', 'strength' => 'High Potency', 'company' => 'Abbott / Healthcare'],
            ['brand_name' => 'Cee-Vit 250mg', 'generic_name' => 'Vitamin C (Ascorbic Acid)', 'dosage_form' => 'Tab', 'strength' => '250mg Chewable', 'company' => 'Square Pharma'],
            ['brand_name' => 'Fefol-Z', 'generic_name' => 'Carbonyl Iron + Folic Acid + Zinc', 'dosage_form' => 'Cap', 'strength' => '50mg+0.5mg+61.8mg', 'company' => 'Beximco Pharma'],
            ['brand_name' => 'Omega-3 1000mg', 'generic_name' => 'Omega-3 Fish Oil', 'dosage_form' => 'Cap', 'strength' => '1000mg', 'company' => 'Square Pharmaceuticals Ltd.', 'unit_price' => 15.00],

            // Diabetes & Endocrinology
            ['brand_name' => 'Comet 500mg', 'generic_name' => 'Metformin Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '500mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Comet 850mg', 'generic_name' => 'Metformin Hydrochloride', 'dosage_form' => 'Tab', 'strength' => '850mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Daoryl 2mg', 'generic_name' => 'Glimepiride', 'dosage_form' => 'Tab', 'strength' => '2mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Januvia 100mg', 'generic_name' => 'Sitagliptin', 'dosage_form' => 'Tab', 'strength' => '100mg', 'company' => 'MSD / Square Pharma'],
            ['brand_name' => 'Linaglip 5mg', 'generic_name' => 'Linagliptin', 'dosage_form' => 'Tab', 'strength' => '5mg', 'company' => 'Incepta Pharma'],
            ['brand_name' => 'Thyrox 50mcg', 'generic_name' => 'Levothyroxine Sodium', 'dosage_form' => 'Tab', 'strength' => '50mcg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Thyrox 100mcg', 'generic_name' => 'Levothyroxine Sodium', 'dosage_form' => 'Tab', 'strength' => '100mcg', 'company' => 'Square Pharma'],

            // Eye, Ear & Dental
            ['brand_name' => 'Moxan Eye Drops', 'generic_name' => 'Moxifloxacin', 'dosage_form' => 'Drop', 'strength' => '0.5%', 'company' => 'Square Pharma'],
            ['brand_name' => 'Tears Naturale', 'generic_name' => 'Dextran 70 + Hypromellose', 'dosage_form' => 'Drop', 'strength' => 'Lubricant Eye Drop', 'company' => 'Alcon / Novartis'],
            ['brand_name' => 'Orasore Gel', 'generic_name' => 'Choline Salicylate + Benzalkonium', 'dosage_form' => 'Ointment', 'strength' => '10g', 'company' => 'Square Pharma'],
            ['brand_name' => 'Betameson-N Cream', 'generic_name' => 'Betamethasone + Neomycin', 'dosage_form' => 'Ointment', 'strength' => '15g', 'company' => 'Square Pharma'],
            ['brand_name' => 'Daktarin Oral Gel', 'generic_name' => 'Miconazole Nitrate', 'dosage_form' => 'Ointment', 'strength' => '20g', 'company' => 'Janssen / Healthcare'],

            // Anti-Anxiety & CNS
            ['brand_name' => 'Rivotril 0.5mg', 'generic_name' => 'Clonazepam', 'dosage_form' => 'Tab', 'strength' => '0.5mg', 'company' => 'Roche / Radiant'],
            ['brand_name' => 'Disopan 0.5mg', 'generic_name' => 'Clonazepam', 'dosage_form' => 'Tab', 'strength' => '0.5mg', 'company' => 'Square Pharma'],
            ['brand_name' => 'Parnox 0.5mg', 'generic_name' => 'Alprazolam', 'dosage_form' => 'Tab', 'strength' => '0.5mg', 'company' => 'Square Pharma'],
        ];

        foreach ($medicines as $med) {
            Medicine::updateOrCreate(
                ['brand_name' => $med['brand_name']],
                $med
            );
        }
    }
}
