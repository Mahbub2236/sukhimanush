<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Doctor;
use App\Models\FamilyMember;
use App\Models\HealthMetric;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the SaaS multi-tenant doctor platform database.
     */
    public function run(): void
    {
        // 1. Super Admin User
        $superAdmin = User::create([
            'name' => 'Sukhi Manush Super Admin',
            'email' => 'admin@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+8801811223344',
            'address' => 'Gulshan 2, Dhaka, Bangladesh',
        ]);

        // 2. Patient User
        $patientUser = User::create([
            'name' => 'মোঃ মাহবুবুর রহমান',
            'email' => 'patient@sukhimanush.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '01711223344',
            'address' => 'মিরপুর ডিওএইচএস, ঢাকা, বাংলাদেশ',
            'avatar' => 'images/team/test-thumb2.jpg',
        ]);

        // 2.1 Family Members for Patient
        $familyFather = FamilyMember::create([
            'user_id' => $patientUser->id,
            'name' => 'কামাল হোসেন (বাবা)',
            'relationship' => 'Father',
            'gender' => 'Male',
            'age_or_dob' => '৬২ বছর',
            'blood_group' => 'B+',
            'medical_notes' => 'টাইপ-২ ডায়াবেটিস ও উচ্চ রক্তচাপের হিস্ট্রি রয়েছে।',
        ]);

        $familyMother = FamilyMember::create([
            'user_id' => $patientUser->id,
            'name' => 'সালমা বেগম (মা)',
            'relationship' => 'Mother',
            'gender' => 'Female',
            'age_or_dob' => '৫৫ বছর',
            'blood_group' => 'O+',
            'medical_notes' => 'হাঁটুর বাত ও হালকা গ্যাস্ট্রিক সমস্যা।',
        ]);

        // 2.2 Health Metrics for Patient & Family
        HealthMetric::create([
            'user_id' => $patientUser->id,
            'family_member_id' => $familyFather->id,
            'metric_type' => 'blood_sugar',
            'value_primary' => 7.4,
            'measured_time' => 'খালি পেটে (Fasting)',
            'recorded_date' => now()->subDays(1)->format('Y-m-d'),
            'notes' => 'সকালে হাঁটার পর টেস্ট করা হয়েছে।',
        ]);

        HealthMetric::create([
            'user_id' => $patientUser->id,
            'family_member_id' => $familyFather->id,
            'metric_type' => 'blood_pressure',
            'value_primary' => 130,
            'value_secondary' => 85,
            'measured_time' => 'বিকেলে',
            'recorded_date' => now()->format('Y-m-d'),
            'notes' => 'রক্তচাপ স্বাভাবিক নিয়ন্ত্রণে আছে।',
        ]);

        // 3. Doctors with BMDC Numbers, 64 Districts, Verified Badges, and Telemedicine
        $doctorsSeedData = [
            [
                'user' => [
                    'name' => 'ডাঃ থমাস হেনরি (Dr. Thomas Henry)',
                    'email' => 'dr.thomas@sukhimanush.com',
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => '01711001122',
                    'address' => 'ধানমন্ডি, ঢাকা, বাংলাদেশ',
                ],
                'doctor' => [
                    'name' => 'ডাঃ থমাস হেনরি (Dr. Thomas Henry)',
                    'slug' => 'dr-thomas-henry',
                    'specialization' => 'কার্ডিওলজি (Cardiology)',
                    'designation' => 'চিফ ইন্টারভেনশনাল কার্ডিওলজিস্ট',
                    'qualifications' => 'MBBS, MD (Cardiology), FACC, FSCAI (USA)',
                    'bmdc_number' => 'BMDC-A-45892',
                    'division' => 'Dhaka',
                    'district' => 'Dhaka',
                    'upazila' => 'ধানমন্ডি (Dhanmondi)',
                    'bio' => 'ডাঃ থমাস হেনরি ১৬ বছরেরও বেশি অভিজ্ঞতাসম্পন্ন কার্ডিওলজিস্ট। উচ্চ রক্তচাপ, হার্ট অ্যাটাক পরবর্তী যত্ন ও হৃদরোগ চিকিৎসায় বিশেষজ্ঞ।',
                    'avatar' => 'images/team/1.jpg',
                    'email' => 'dr.thomas@sukhimanush.com',
                    'phone' => '01711001122',
                    'chamber_address' => 'পপুলার ডায়াগনস্টিক সেন্টার, হাউজ #১৬, রোড #২, ধানমন্ডি, ঢাকা',
                    'city' => 'Dhaka',
                    'consultation_fee' => 1000.00,
                    'telemedicine_fee' => 800.00,
                    'telemedicine_link' => 'https://meet.google.com/sukhimanush-heart',
                    'experience_years' => 16,
                    'available_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    'start_time' => '17:00:00',
                    'end_time' => '21:00:00',
                    'slot_duration_minutes' => 20,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'is_emergency_available' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'ডাঃ ক্লারা আলেকজান্ডার (Dr. Clara Alexander)',
                    'email' => 'dr.clara@sukhimanush.com',
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => '01711001133',
                    'address' => 'পাঁচলাইশ, চট্টগ্রাম',
                ],
                'doctor' => [
                    'name' => 'ডাঃ ক্লারা আলেকজান্ডার (Dr. Clara Alexander)',
                    'slug' => 'dr-clara-alexander',
                    'specialization' => 'শিশু ও কিশোর রোগ (Pediatrics)',
                    'designation' => 'সিনিয়র কনসালটেন্ট পেডিয়াট্রিশিয়ান',
                    'qualifications' => 'MBBS, DCH, MRCPCH (UK), FCPS (Pediatrics)',
                    'bmdc_number' => 'BMDC-A-51204',
                    'division' => 'Chittagong',
                    'district' => 'Chittagong',
                    'upazila' => 'পাঁচলাইশ (Panchlaish)',
                    'bio' => 'নবজাতক ও শিশু বিশেষজ্ঞ। শিশুদের পুষ্টি, বিকাশ ও সাধারণ রোগের চিকিৎসায় বিশেষ পারদর্শী।',
                    'avatar' => 'images/team/3.jpg',
                    'email' => 'dr.clara@sukhimanush.com',
                    'phone' => '01711001133',
                    'chamber_address' => 'সিএসসিআর হাসপাতাল, ও.আর. নিজাম রোড, পাঁচলাইশ, চট্টগ্রাম',
                    'city' => 'Chittagong',
                    'consultation_fee' => 800.00,
                    'telemedicine_fee' => 600.00,
                    'telemedicine_link' => 'https://meet.google.com/sukhimanush-child',
                    'experience_years' => 12,
                    'available_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'start_time' => '16:00:00',
                    'end_time' => '20:00:00',
                    'slot_duration_minutes' => 20,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'is_emergency_available' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'ডাঃ হ্যারিসন স্যামুয়েল (Dr. Harrison Samuel)',
                    'email' => 'dr.harrison@sukhimanush.com',
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => '01711001144',
                    'address' => 'চৌহাট্টা, সিলেট',
                ],
                'doctor' => [
                    'name' => 'ডাঃ হ্যারিসন স্যামুয়েল (Dr. Harrison Samuel)',
                    'slug' => 'dr-harrison-samuel',
                    'specialization' => 'নিউরো মেডিসিন ও ব্রেন (Neurology)',
                    'designation' => 'কনসালটেন্ট নিউরোলজিস্ট',
                    'qualifications' => 'MBBS, MS (Neuro Surgery), FRCS (Edin)',
                    'bmdc_number' => 'BMDC-A-38901',
                    'division' => 'Sylhet',
                    'district' => 'Sylhet',
                    'upazila' => 'কোতোয়ালী (Kotwali)',
                    'bio' => 'স্ট্রোক, মাথা ব্যথা, মাইগ্রেন ও নার্ভের জটিল সমস্যায় দীর্ঘ ১৫ বছর ধরে সফল চিকিৎসা সেবা দিচ্ছেন।',
                    'avatar' => 'images/team/2.jpg',
                    'email' => 'dr.harrison@sukhimanush.com',
                    'phone' => '01711001144',
                    'chamber_address' => 'মাউন্ট এডোরা হসপিটাল, আখালিয়া, সিলেট',
                    'city' => 'Sylhet',
                    'consultation_fee' => 1200.00,
                    'telemedicine_fee' => 1000.00,
                    'telemedicine_link' => 'https://meet.google.com/sukhimanush-neuro',
                    'experience_years' => 15,
                    'available_days' => ['Monday', 'Wednesday', 'Friday', 'Saturday'],
                    'start_time' => '15:00:00',
                    'end_time' => '19:00:00',
                    'slot_duration_minutes' => 30,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'is_emergency_available' => false,
                ],
            ],
            [
                'user' => [
                    'name' => 'ডাঃ মার্কাস ব্রোডি (Dr. Marcus Brody)',
                    'email' => 'dr.marcus@sukhimanush.com',
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => '01711001155',
                    'address' => 'গ্রেটার রোড, রাজশাহী',
                ],
                'doctor' => [
                    'name' => 'ডাঃ মার্কাস ব্রোডি (Dr. Marcus Brody)',
                    'slug' => 'dr-marcus-brody',
                    'specialization' => 'ডেন্টাল ও মুখরোগ বিশেষজ্ঞ (Dental Care)',
                    'designation' => 'কসমেটিক ও ডেন্টাল ইমপ্ল্যান্ট সার্জন',
                    'qualifications' => 'BDS, MDS (Oral Surgery), PGT (Dental)',
                    'bmdc_number' => 'BMDC-D-12845',
                    'division' => 'Rajshahi',
                    'district' => 'Rajshahi',
                    'upazila' => 'বোয়ালিয়া (Boalia)',
                    'bio' => 'ব্যথামুক্ত রুট ক্যানেল, দাঁত স্কেলিং ও ডেন্টাল ইমপ্ল্যান্টের আধুনিক চিকিৎসা।',
                    'avatar' => 'images/team/4.jpg',
                    'email' => 'dr.marcus@sukhimanush.com',
                    'phone' => '01711001155',
                    'chamber_address' => 'রয়্যাল ডেন্টাল ক্লিনিক, রানীবাজার, রাজশাহী',
                    'city' => 'Rajshahi',
                    'consultation_fee' => 600.00,
                    'telemedicine_fee' => 500.00,
                    'telemedicine_link' => 'https://meet.google.com/sukhimanush-dental',
                    'experience_years' => 11,
                    'available_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'start_time' => '10:00:00',
                    'end_time' => '18:00:00',
                    'slot_duration_minutes' => 25,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'is_emergency_available' => true,
                ],
            ],
        ];

        $doctors = [];
        foreach ($doctorsSeedData as $item) {
            $user = User::create($item['user']);
            $doctorData = $item['doctor'];
            $doctorData['user_id'] = $user->id;
            $doctor = Doctor::create($doctorData);
            $doctors[$doctor->slug] = $doctor;
        }

        // 4. Create Sample Appointments
        $drThomas = $doctors['dr-thomas-henry'];
        $drClara = $doctors['dr-clara-alexander'];

        // Completed Appointment with Dr. Thomas
        $apt1 = Appointment::create([
            'appointment_number' => 'DOC-2026-8942',
            'doctor_id' => $drThomas->id,
            'patient_id' => $patientUser->id,
            'family_member_id' => $familyFather->id,
            'patient_name' => 'কামাল হোসেন (বাবা)',
            'patient_email' => $patientUser->email,
            'patient_phone' => $patientUser->phone,
            'appointment_date' => now()->subDays(2)->format('Y-m-d'),
            'appointment_time' => '05:30 PM',
            'serial_number' => 1,
            'current_serving_serial' => 1,
            'consultation_fee' => 1000.00,
            'consultation_type' => 'in_chamber',
            'district' => 'Dhaka',
            'payment_status' => 'paid',
            'payment_method' => 'bkash',
            'status' => 'completed',
            'reason_for_visit' => 'বুকের মাঝে চাপ অনুভূতি ও নিয়মিত প্রেসার চেকআপ।',
            'doctor_notes' => 'রোগীর মাইল্ড হাইপারটেনশন রয়েছে। নিয়মিত ওষুধ ও হাঁটার পরামর্শ দেওয়া হলো।',
        ]);

        // Payment for Appointment 1 (10% Admin Commission, 90% Doctor Earning)
        Payment::create([
            'transaction_id' => 'TXN-BKASH-894201',
            'appointment_id' => $apt1->id,
            'doctor_id' => $drThomas->id,
            'patient_id' => $patientUser->id,
            'amount' => 1000.00,
            'admin_commission' => 100.00,
            'doctor_earning' => 900.00,
            'payment_method' => 'bkash',
            'status' => 'completed',
        ]);

        // 5. Digital Prescription issued by Dr. Thomas
        Prescription::create([
            'prescription_number' => 'RX-2026-1049',
            'appointment_id' => $apt1->id,
            'doctor_id' => $drThomas->id,
            'patient_id' => $patientUser->id,
            'patient_name' => 'কামাল হোসেন (বাবা)',
            'patient_age_gender' => '৬২ বছর / পুরুষ',
            'diagnosis' => 'মাইল্ড হাইপারটেনশন (Mild Essential Hypertension), বর্ডারলাইন কোলেস্টেরল',
            'medicines' => [
                [
                    'name' => 'Tab. Nebita 5mg (Nebivolol)',
                    'dosage' => '1 + 0 + 0',
                    'duration' => '৩০ দিন',
                    'instruction' => 'প্রতিদিন সকালে নাস্তার পর',
                    'company' => 'Square Pharmaceuticals Ltd.',
                    'unit_price' => 8.00,
                ],
                [
                    'name' => 'Tab. Lipicon 10mg (Atorvastatin)',
                    'dosage' => '0 + 0 + 1',
                    'duration' => '৩০ দিন',
                    'instruction' => 'প্রতিদিন রাতে খাবারের পর',
                    'company' => 'Eskayef Pharmaceuticals Ltd.',
                    'unit_price' => 12.00,
                ],
                [
                    'name' => 'Cap. Omega-3 1000mg',
                    'dosage' => '0 + 1 + 0',
                    'duration' => '৬০ দিন',
                    'instruction' => 'প্রতিদিন দুপুরে খাওয়ার পর',
                    'company' => 'Square Pharmaceuticals Ltd.',
                    'unit_price' => 15.00,
                ],
            ],
            'medical_tests' => [
                'Complete Blood Count (CBC)',
                'Fasting Lipid Profile',
                'Serum Creatinine',
                '12-Lead ECG',
            ],
            'advice' => "১. খাবারে কাঁচা লবণ সম্পূর্ণ পরিহার করুন।\n২. প্রতিদিন সকালে ৩০ মিনিট হাঁটুন।\n৩. তৈলাক্ত ও ভাজাপোড়া খাবার এড়িয়ে চলুন।\n৪. কোনো অস্বস্তি লাগলে নিকটস্থ হাসপাতালে যোগাযোগ করুন।",
            'follow_up_date' => now()->addDays(28)->format('Y-m-d'),
        ]);

        // Upcoming Confirmed Appointment
        $apt2 = Appointment::create([
            'appointment_number' => 'DOC-2026-3195',
            'doctor_id' => $drClara->id,
            'patient_id' => $patientUser->id,
            'patient_name' => $patientUser->name,
            'patient_email' => $patientUser->email,
            'patient_phone' => $patientUser->phone,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '06:00 PM',
            'serial_number' => 2,
            'current_serving_serial' => 1,
            'consultation_fee' => 600.00,
            'consultation_type' => 'online_video',
            'district' => 'Chittagong',
            'meeting_link' => 'https://meet.google.com/sukhimanush-child',
            'payment_status' => 'paid',
            'payment_method' => 'nagad',
            'status' => 'confirmed',
            'reason_for_visit' => 'অনলাইন ভিডিও কলে শিশু স্বাস্থ্য ও এলার্জি পরামর্শ।',
        ]);

        Payment::create([
            'transaction_id' => 'TXN-NAGAD-319502',
            'appointment_id' => $apt2->id,
            'doctor_id' => $drClara->id,
            'patient_id' => $patientUser->id,
            'amount' => 600.00,
            'admin_commission' => 60.00,
            'doctor_earning' => 540.00,
            'payment_method' => 'nagad',
            'status' => 'completed',
        ]);

        // 6. Testimonials
        $testimonials = [
            [
                'patient_name' => 'আফরোজা খানম',
                'title' => 'কার্ডিয়াক কেয়ার রোগী',
                'review' => 'ডাঃ থমাস স্যারের প্রেসক্রিপশন বাংলায় অডিওতে শুনতে পেরে মা খুব সহজেই ওষুধের নিয়ম বুঝতে পেরেছেন। দারুণ সার্ভিস!',
                'rating' => 5,
                'avatar' => 'images/team/test-thumb1.jpg',
            ],
            [
                'patient_name' => 'তানভীরুল ইসলাম',
                'title' => 'টেলিমেডিসিন সেবাগ্রহীতা',
                'review' => 'চট্টগ্রাম না গিয়েও সিলেট বসে ভিডিও কলে ডাক্তার দেখিয়ে প্রেসক্রিপশন পেয়ে গেলাম। বিকাশে পেমেন্টও খুব সহজ ছিল।',
                'rating' => 5,
                'avatar' => 'images/team/test-thumb3.jpg',
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }

        // 7. Blog Posts
        $blogs = [
            [
                'title' => 'হৃদরোগ প্রতিরোধ ও রক্তচাপ নিয়ন্ত্রণে রাখার সেরা ৫ উপায়',
                'slug' => 'heart-health-tips-bangladesh',
                'category' => 'কার্ডিওলজি',
                'author_name' => 'ডাঃ থমাস হেনরি',
                'image' => 'images/blog/blog-1.jpg',
                'excerpt' => 'দৈনন্দিন খাবারে কাঁচা লবণ পরিহার ও নিয়মিত ৩০ মিনিট হাঁটা হার্ট ভালো রাখার সবচেয়ে সহজ উপায়।',
                'content' => 'হৃদরোগ প্রতিরোধে নিয়মিত রক্তচাপ ও ব্লাড সুগার পরীক্ষা করা অত্যন্ত জরুরি। অতিরিক্ত মানসিক চাপ পরিহার করুন এবং চিকিৎসকের পরামর্শ ছাড়া প্রেসারের ওষুধ বন্ধ করবেন না।',
                'views_count' => 620,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'শিশুদের ঋতু পরিবর্তনের এলার্জি ও শ্বাসকষ্টে করণীয়',
                'slug' => 'child-allergy-pediatric-guide',
                'category' => 'শিশু স্বাস্থ্য',
                'author_name' => 'ডাঃ ক্লারা আলেকজান্ডার',
                'image' => 'images/blog/blog-2.jpg',
                'excerpt' => 'শীত বা বর্ষার শুরুতে শিশুদের ধুলোবালি ও ঠান্ডা বাতাস থেকে নিরাপদ রাখুন।',
                'content' => 'শিশুর রোগ প্রতিরোধ ক্ষমতা বাড়াতে সুষম পুষ্টিকর খাবার এবং বিশুদ্ধ পানির অভ্যাস গড়ে তুলুন। কোনো ধরনের দীর্ঘমেয়াদী শ্বাসকষ্ট অবহেলা করবেন না।',
                'views_count' => 450,
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($blogs as $b) {
            BlogPost::create($b);
        }

        // 8. Contact Message
        ContactMessage::create([
            'name' => 'ডাঃ সাইফ আহমেদ',
            'email' => 'dr.saif@gmail.com',
            'phone' => '01700998877',
            'subject' => 'ডাক্তার রেজিস্ট্রেশন ও অনবোর্ডিং সংক্রান্ত তথ্য',
            'message' => 'আসসালামু আলাইকুম, আমি খুলনা শহরের একজন অর্থোপেডিক কনসালটেন্ট। আপনাদের প্লাটফর্মে আমার চেম্বার ও টেলিমেডিসিন যুক্ত করতে চাই। বিস্তারিত জানাবেন।',
            'is_read' => false,
        ]);

        // 9. Bangladesh Healthcare Legal Policies & Terms
        (new LegalPolicySeeder)->run();
    }
}
