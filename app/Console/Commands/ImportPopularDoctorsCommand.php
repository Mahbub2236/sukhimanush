<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportPopularDoctorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:popular {--limit= : Maximum number of doctors to import} {--refresh : Force re-download from Popular Diagnostic}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all specialist doctors from Popular Diagnostic Centre Ltd with pure Bangla translation & full details';

    /**
     * Popular Diagnostic Branches
     */
    protected static array $branches = [
        1 => ['name' => 'ধানমন্ডি', 'en' => 'Dhanmondi', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Dhanmondi', 'address' => 'বাড়ি # ১৬, রোড # ২, ধানমন্ডি, ঢাকা-১২০৫', 'phone' => '09666 787801'],
        6 => ['name' => 'সাভার', 'en' => 'Savar', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Savar', 'address' => 'ই/২২, তালবাগ, আনন্দপুর, সাভার, ঢাকা', 'phone' => '09666 787808'],
        7 => ['name' => 'উত্তরা জসিম উদ্দিন (সেক্টর-৪)', 'en' => 'Uttara Jashim Uddin', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Uttara', 'address' => 'বাড়ি # ২১, রোড # ৭, সেক্টর # ৪, জসিম উদ্দিন মোড়, উত্তরা, ঢাকা-১২৩০', 'phone' => '09666 787805'],
        9 => ['name' => 'শ্যামলী', 'en' => 'Shyamoli', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Mohammadpur', 'address' => '২২/৭ এ এস এম নুরুজ্জামান রোড, ব্লক-বি, বাবর রোড, মোহাম্মদপুর, ঢাকা-১২০৭', 'phone' => '09666 787806'],
        10 => ['name' => 'মিরপুর', 'en' => 'Mirpur', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Mirpur', 'address' => 'বাড়ি # ৬৭, এভিনিউ # ৫, ব্লক # সি, সেকশন-৬, মিরপুর-১০, পল্লবী, ঢাকা', 'phone' => '09666 787807'],
        13 => ['name' => 'বগুড়া', 'en' => 'Bogura', 'city' => 'Bogura', 'division' => 'Rajshahi', 'district' => 'Bogura', 'upazila' => 'Bogura Sadar', 'address' => 'বাড়ি # ১৪/১৯৭৯, রহমান মার্কেট, ছিলিমপুর, বগুড়া', 'phone' => '09666 787812'],
        18 => ['name' => 'বাড্ডা', 'en' => 'Badda', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Badda', 'address' => 'চ-৯০/২, উত্তর বাড্ডা (প্রগতি সরণি), ঢাকা-১২১২', 'phone' => '09666 787809'],
        31 => ['name' => 'নোয়াখালী', 'en' => 'Noakhali', 'city' => 'Maijdee', 'division' => 'Chattogram', 'district' => 'Noakhali', 'upazila' => 'Noakhali Sadar', 'address' => 'আনোয়ারা টাওয়ার, নতুন বাসস্ট্যান্ড, হোল্ডিং নং: ১২২২ (মাইজদী স্টেডিয়ামের কাছে), মাইজদী কোর্ট, নোয়াখালী', 'phone' => '09666 787818'],
        33 => ['name' => 'খুলনা', 'en' => 'Khulna', 'city' => 'Khulna', 'division' => 'Khulna', 'district' => 'Khulna', 'upazila' => 'Khulna Sadar', 'address' => '৩৭ কেডিএ এভিনিউ, খুলনা', 'phone' => '09666 787821'],
        34 => ['name' => 'যাত্রাবাড়ী', 'en' => 'Jatrabari', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Jatrabari', 'address' => '৩৩, শহীদ ফারুক সড়ক, উত্তর যাত্রাবাড়ী, ঢাকা-১২০৪', 'phone' => '09666 787822'],
        35 => ['name' => 'উত্তরা গরীব-ই-নেওয়াজ (সেক্টর-১৩)', 'en' => 'Uttara Garib E Newaz', 'city' => 'Dhaka', 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => 'Uttara', 'address' => 'বাড়ি # ৪১, গরীব-ই-নেওয়াজ এভিনিউ, সেক্টর # ১৩, উত্তরা, ঢাকা', 'phone' => '09666 787823'],
        36 => ['name' => 'টাঙ্গাইল', 'en' => 'Tangail', 'city' => 'Tangail', 'division' => 'Dhaka', 'district' => 'Tangail', 'upazila' => 'Tangail Sadar', 'address' => 'পপুলার ডায়াগনস্টিক সেন্টার, টাঙ্গাইল', 'phone' => '09666 787824'],
        37 => ['name' => 'কুমিল্লা', 'en' => 'Cumilla', 'city' => 'Cumilla', 'division' => 'Chattogram', 'district' => 'Cumilla', 'upazila' => 'Cumilla Sadar', 'address' => 'কান্দিরপাড়, লাকসাম রোড, কুমিল্লা', 'phone' => '09666 787825'],
    ];

    /**
     * Pure Bangla Specialty Map (100% of Popular Diagnostic Specialties)
     */
    protected static array $specialtyMap = [
        'Breast Cancer Specialist' => 'স্তন ক্যান্সার বিশেষজ্ঞ',
        'Breast, Colorectal & Laparoscopy Surgery' => 'ব্রেস্ট, কোলোরেক্টাল ও ল্যাপারোস্কোপিক সার্জন',
        'Cardiac surgery' => 'কার্ডিয়াক ও হৃদরোগ সার্জন',
        'Cardiology' => 'কার্ডিওলজি ও হৃদরোগ বিশেষজ্ঞ',
        'Chest Medicine' => 'বক্ষব্যাধি ও মেডিসিন বিশেষজ্ঞ',
        'Child Neurology' => 'শিশু নিউরোলজি বিশেষজ্ঞ',
        'Child/Paediatrics' => 'শিশু ও কিশোর রোগ বিশেষজ্ঞ',
        'Colorectal Surgery' => 'কোলোরেক্টাল ও পায়ুপথ সার্জন',
        'Critical Care Medicine' => 'ক্রিটিকাল কেয়ার ও আইসিইউ মেডিসিন বিশেষজ্ঞ',
        'Diabetologist' => 'ডায়াবেটিস ও হরমোন বিশেষজ্ঞ',
        'ENT, Head & Neck Surgery' => 'নাক, কান, গলা রোগ বিশেষজ্ঞ ও হেড-নেক সার্জন',
        'Endocrine Medicine' => 'এন্ডোক্রাইনোলজি ও হরমোন বিশেষজ্ঞ',
        'Eye / Ophthalmology' => 'চক্ষু বিশেষজ্ঞ ও সার্জন',
        'Food & Nutrition' => 'খাদ্য ও পুষ্টি বিশেষজ্ঞ',
        'Gastroenterology' => 'গ্যাস্ট্রোএন্টারোলজি ও পরিপাকতন্ত্র বিশেষজ্ঞ',
        'General & Oncology Surgery' => 'জেনারেল ও ক্যান্সার সার্জন',
        'General & Surgical Oncology' => 'অনকোলজি ও ক্যান্সার সার্জন',
        'General Surgery' => 'জেনারেল সার্জন',
        'Gyane Oncology' => 'গাইনী অনকোলজি ও স্ত্রীরোগ ক্যান্সার বিশেষজ্ঞ',
        'Gynaecology' => 'স্ত্রীরোগ ও প্রসূতি বিশেষজ্ঞ (গাইনী)',
        'Haematology' => 'রক্তরোগ বিশেষজ্ঞ (হেমাটোলজি)',
        'Hepatology' => 'লিভার ও পরিপাকতন্ত্র বিশেষজ্ঞ',
        'Infertility & Gynae' => 'বন্ধ্যাত্ব ও স্ত্রীরোগ বিশেষজ্ঞ',
        'Liver Medicine' => 'হেপাটোলজি ও লিভার বিশেষজ্ঞ',
        'Liver, Biliary And Pancreatic Surgery' => 'লিভার, পিত্তথলি ও অগ্ন্যাশয় সার্জন',
        'Medicine' => 'মেডিসিন বিশেষজ্ঞ',
        'Nephrology/Kidney Medicine' => 'কিডনি ও মেডিসিন বিশেষজ্ঞ (নেফ্রোলজি)',
        'Neuro Surgery' => 'নিউরোসার্জন (মস্তিষ্ক ও স্পাইন সার্জন)',
        'Neurology' => 'নিউরো মেডিসিন ও স্নায়ুরোগ বিশেষজ্ঞ',
        'Oncology Surgery' => 'অনকোলজি ও ক্যান্সার সার্জন',
        'Oncology/Cancer' => 'ক্যান্সার ও অনকোলজি বিশেষজ্ঞ',
        'Orthopaedic Surgery' => 'হাড়, জোড়া ও অর্থোপেডিক সার্জন',
        'Paediatric Gastroenterology & Nutrition' => 'শিশু পরিপাকতন্ত্র ও পুষ্টি বিশেষজ্ঞ',
        'Paediatric Surgery' => 'শিশু সার্জন',
        'Pain Management' => 'ব্যথানাশক ও পেইন ম্যানেজমেন্ট বিশেষজ্ঞ',
        'Pediatric Cardiology' => 'শিশু কার্ডিওলজি ও হৃদরোগ বিশেষজ্ঞ',
        'Pediatric Neurosurgery' => 'শিশু নিউরোসার্জন',
        'Pediatric Orthopedic' => 'শিশু অর্থোপেডিক সার্জন',
        'Pediatric Surgery' => 'শিশু সার্জন',
        'Physical  Medicine & Rehabilitation' => 'ফিজিক্যাল মেডিসিন ও রিহ্যাবিলিটেশন বিশেষজ্ঞ',
        'Physiotherapy  Department' => 'ফিজিওথেরাপি বিশেষজ্ঞ',
        'Plastic Surgery' => 'প্লাস্টিক ও কসমেটিক সার্জন',
        'Psychiatry' => 'মনোরোগ ও মানসিক স্বাস্থ্য বিশেষজ্ঞ',
        'Rheumatology Medicine' => 'রিউমাটোলজি ও বাতব্যথা বিশেষজ্ঞ',
        'Skin/Dermatology' => 'চর্ম, এলার্জি ও যৌনরোগ বিশেষজ্ঞ',
        'Sports and Exercise Medicine' => 'স্পোর্টস মেডিসিন বিশেষজ্ঞ',
        'Thyroid & Nuclear Medicine' => 'থাইরয়েড ও নিউক্লিয়ার মেডিসিন বিশেষজ্ঞ',
        'Urology Surgery' => 'ইউরোলজি ও মূত্ররোগ সার্জন',
        'Vascular Surgery' => 'রক্তনালী সার্জন (ভাসকুলার সার্জারি)',
        'Endocrinology' => 'এন্ডোক্রাইনোলজি ও হরমোন বিশেষজ্ঞ',
        'Neonatal' => 'নবজাতক ও শিশু বিশেষজ্ঞ',
        'Child Haematology & Oncology' => 'শিশু রক্তরোগ ও ক্যান্সার বিশেষজ্ঞ',
        'Forensic Medicine' => 'ফরেনসিক মেডিসিন বিশেষজ্ঞ',
        'Sonologist' => 'সনোলজিস্ট ও আল্ট্রাসনোগ্রাফি বিশেষজ্ঞ',
        'Child Cardiology' => 'শিশু কার্ডিওলজি ও হৃদরোগ বিশেষজ্ঞ',
        'Nutritionist' => 'পুষ্টিবিদ ও ডায়েট বিশেষজ্ঞ',
        'Child Neuro Surgery' => 'শিশু নিউরোসার্জন',
        'Cardiovascular & Thoracic Surgery' => 'কার্ডিওভাসকুলার ও থোরাসিক সার্জন',
        'Gynae Oncology' => 'গাইনী অনকোলজি বিশেষজ্ঞ',
        'Dietician' => 'পুষ্টিবিদ ও ডায়েট কনসালট্যান্ট',
        'Child/Paediatrics Chest Medicine' => 'শিশু বক্ষব্যাধি বিশেষজ্ঞ',
        'Paediatric Surgery & Urology' => 'শিশু সার্জারি ও ইউরোলজি বিশেষজ্ঞ',
        'Thyroid' => 'থাইরয়েড ও হরমোন বিশেষজ্ঞ',
        'Child/Paediatric Nephrology' => 'শিশু কিডনি রোগ বিশেষজ্ঞ',
        'Dentist' => 'দন্ত চিকিৎসক ও ডেন্টাল সার্জন',
        'Dental Surgery' => 'দন্ত বিশেষজ্ঞ ও ম্যাক্সিলোফেসিয়াল সার্জন',
        'Nephrology' => 'কিডনি রোগ বিশেষজ্ঞ (নেফ্রোলজি)',
        'Laparoscopic, Colorectal' => 'ল্যাপারোস্কোপিক ও কোলোরেক্টাল সার্জন',
        'ENT' => 'নাক, কান ও গলা রোগ বিশেষজ্ঞ',
        'Orthopedics' => 'অর্থোপেডিক ও হাড়ভাঙা বিশেষজ্ঞ',
        'Dermatology' => 'চর্ম ও যৌন রোগ বিশেষজ্ঞ',
        'Physical Medicine' => 'ফিজিক্যাল মেডিসিন বিশেষজ্ঞ',
        'Paediatric Haematology & Oncology' => 'শিশু রক্তরোগ ও ক্যান্সার বিশেষজ্ঞ',
        'Medicine & Cardiology' => 'মেডিসিন ও হৃদরোগ বিশেষজ্ঞ',
        'Pediatric Gastroenterology and Nutrition' => 'শিশু পরিপাকতন্ত্র ও পুষ্টি বিশেষজ্ঞ',
    ];

    /**
     * Dictionary for Bangladeshi Names & Titles
     */
    protected static array $nameDictionary = [
        // Titles
        'md' => 'মোঃ', 'mohammad' => 'মোহাম্মদ', 'mohammed' => 'মোহাম্মদ', 'muhammad' => 'মুহাম্মদ',
        'mst' => 'মোছাঃ', 'most' => 'মোছাঃ', 'engr' => 'প্রকৌঃ',
        'abu' => 'আবু', 'abdul' => 'আব্দুল', 'abdullah' => 'আব্দুল্লাহ', 'abul' => 'আবুল', 'al' => 'আল',
        'syed' => 'সৈয়দ', 'sheikh' => 'শেখ', 'kazi' => 'কাজী', 'mirza' => 'মির্জা', 'sardar' => 'সরদার',
        'bhuiyan' => 'ভূঁইয়া', 'munshi' => 'মুন্সী', 'khandaker' => 'খন্দকার', 'khondker' => 'খন্দকার', 'khondoker' => 'খন্দকার',
        'chowdhury' => 'চৌধুরী', 'choudhury' => 'চৌধুরী', 'majumder' => 'মজুমদার', 'mojumder' => 'মজুমদার',
        'talukdar' => 'তালুকদার', 'tarafdar' => 'তরফদার', 'sikder' => 'সিকদার', 'mollah' => 'মোল্লা', 'gazi' => 'গাজী',
        'mia' => 'মিয়া', 'miah' => 'মিয়া', 'dewan' => 'দেওয়ান',

        // Common Surnames
        'ahmed' => 'আহমেদ', 'ahmad' => 'আহমদ', 'ali' => 'আলী', 'hasan' => 'হাসান', 'hassan' => 'হাসান',
        'hossain' => 'হোসেন', 'hussain' => 'হোসেন', 'rahman' => 'রহমান', 'khan' => 'খান', 'islam' => 'ইসলাম',
        'begum' => 'বেগম', 'khatun' => 'খাতুন', 'akter' => 'আক্তার', 'akhter' => 'আক্তার', 'nahar' => 'নাহার',
        'sultana' => 'সুলতানা', 'jahan' => 'জাহান', 'parveen' => 'পারভীন', 'parvin' => 'পারভীন', 'nasrin' => 'নাসরিন',
        'sharmin' => 'শারমিন', 'yasmin' => 'ইয়াসমিন', 'fatema' => 'ফাতেমা', 'farhana' => 'ফারহানা',
        'haque' => 'হক', 'hoque' => 'হক', 'uddin' => 'উদ্দীন', 'ullah' => 'উল্লাহ', 'zaman' => 'জামান',
        'alam' => 'আলম', 'karim' => 'করিম', 'rashid' => 'রশীদ', 'kamal' => 'কামাল', 'mostafa' => 'মোস্তফা',
        'mustafa' => 'মুস্তফা', 'reza' => 'রেজা', 'shah' => 'শাহ', 'bari' => 'বারী', 'basher' => 'বশার', 'bashar' => 'বশার',

        // Hindu Names
        'saha' => 'সাহা', 'ghosh' => 'ঘোষ', 'roy' => 'রায়', 'das' => 'দাস', 'dutta' => 'দত্ত', 'datta' => 'দত্ত',
        'sen' => 'সেন', 'barua' => 'বড়ুয়া', 'debnath' => 'দেবনাথ', 'paul' => 'পাল', 'pal' => 'পাল',
        'bhowmik' => 'ভৌমিক', 'biswas' => 'বিশ্বাস', 'bishwas' => 'বিশ্বাস', 'banik' => 'বণিক', 'kundu' => 'কুন্ডু',
        'chandra' => 'চন্দ্র', 'kumar' => 'কুমার', 'adhikary' => 'অধিকারী', 'adhikari' => 'অধিকারী',

        // Common First / Middle Names
        'ara' => 'আরা', 'banu' => 'বানু', 'nobi' => 'নবী', 'nabi' => 'নবী', 'razzak' => 'রাজ্জাক',
        'kamruzzaman' => 'কামরুজ্জামান', 'sharifuzzaman' => 'শরিফুজ্জামান', 'rezaul' => 'রেজাউল',
        'mahmudul' => 'মাহমুদুল', 'rashedul' => 'রাশেদুল', 'saleh' => 'সালেহ', 'panna' => 'পান্না',
        'indrajit' => 'ইন্দ্রজিৎ', 'indrojit' => 'ইন্দ্রজিৎ', 'ismat' => 'ইসমাত', 'arefin' => 'আরেফিন',
        'morsalin' => 'মোরসালিন', 'sirajul' => 'সিরাজুল', 'shirajul' => 'সিরাজুল', 'man' => 'মান',
        'shirin' => 'শিরিন', 'afsar' => 'আফসার', 'ahamed' => 'আহমেদ', 'benozir' => 'বেনজির',
        'sadia' => 'সাদিয়া', 'jesmin' => 'জেসমিন', 'ilias' => 'ইলিয়াস', 'firoj' => 'ফিরোজ',
        'ibna' => 'ইবনে', 'ibne' => 'ইবনে', 'ahad' => 'আহাদ', 'tusher' => 'তুষার', 'tushar' => 'তুষার',
        'areju' => 'আরেজু', 'taskina' => 'তাসকিনা', 'azizul' => 'আজিজুল', 'mosharof' => 'মোশাররফ',
        'mosharraf' => 'মোশাররফ', 'shimul' => 'শিমুল', 'hazera' => 'হাজেরা', 'zamil' => 'জামিল',
        'zaidur' => 'জাইদুর', 'rahim' => 'রহিম', 'hasinatul' => 'হাসিনাতুল', 'zannat' => 'জান্নাত',
        'mazharul' => 'মাযহারুল', 'tanvir' => 'তানভীর', 'sarwar' => 'সারওয়ার', 'ferdaus' => 'ফেরদৌস',
        'ferdous' => 'ফেরদৌস', 'kafi' => 'কাফি', 'khurshid' => 'খুরশীদ', 'shefin' => 'শেফিন',
        'masuda' => 'মাসুদা', 'baqui' => 'বাকী', 'shaheed' => 'শহীদ', 'shahid' => 'শহীদ',
        'nurannabi' => 'নূরুন্নবী', 'ranu' => 'রানু', 'ruma' => 'রুমা', 'laila' => 'লায়লা',
        'salma' => 'সালমা', 'rokeya' => 'রোকেয়া', 'monir' => 'মনির', 'moniruzzaman' => 'মনিরুজ্জামান',
        'ashraf' => 'আশরাফ', 'anisur' => 'আনিসুর', 'anis' => 'আনিস', 'motiur' => 'মতিউর',
        'mizanur' => 'মিজানুর', 'lutfor' => 'লুৎফর', 'lutfur' => 'লুৎফর', 'sayeed' => 'সাঈদ',
        'saeed' => 'সাঈদ', 'shahidul' => 'শহীদুল', 'nazrul' => 'নজরুল',
        'enamul' => 'এনামুল', 'ziaur' => 'জিয়াউর', 'habibur' => 'হাবিবুর', 'saiful' => 'সাইফুল',
        'tariqul' => 'তরিকুল', 'zahid' => 'জাহিদ', 'rashed' => 'রাশেদ', 'faisal' => 'ফয়সাল',
        'imran' => 'ইমরান', 'arif' => 'আরিফ', 'tareq' => 'তারেক', 'shohel' => 'সোহেল',
        'sumon' => 'সুমন', 'shakil' => 'শাকিল', 'rubel' => 'রুবেল', 'ripon' => 'রিপন',
        'shuvo' => 'শুভ', 'sabbir' => 'সাব্বির', 'nayeem' => 'নাঈম', 'tamim' => 'তামিম',
        'rifat' => 'রিফাত', 'siam' => 'সিয়াম', 'arafat' => 'আরাফাত', 'riaz' => 'রিয়াজ',
        'anwar' => 'আনোয়ার', 'golam' => 'গোলাম', 'shamsul' => 'শামসুল', 'fazlul' => 'ফজলুল',
        'asif' => 'আসিফ', 'adnan' => 'আদনান', 'shahin' => 'শাহীন', 'sohag' => 'সোহাগ',
        'mehedi' => 'মেহেদী', 'nazmul' => 'নাজমুল', 'ashik' => 'আশিক', 'sakib' => 'সাকিব',
        'noman' => 'নোমান', 'helal' => 'হেলাল', 'habib' => 'হাবিব', 'harun' => 'হারুন',
        'selim' => 'সেলিম', 'salim' => 'সালিম', 'mamun' => 'মামুন', 'monjur' => 'মঞ্জুর',
        'manzur' => 'মনজুর', 'jahangir' => 'জাহাঙ্গীর', 'alamgir' => 'আলমগীর', 'jahidul' => 'জাহিদুল',
        'zakir' => 'জাকির', 'zobaida' => 'জুবাইদা', 'tasnim' => 'তাসনিম', 'tanzila' => 'তানজিলা',
        'sabrina' => 'সাবরিনা', 'nadia' => 'নাদিয়া', 'nusrat' => 'নুসরাত', 'nargis' => 'নার্গিস',
        'samina' => 'সামিনা', 'shahana' => 'শাহানা', 'rebeka' => 'রেবেকা', 'afroza' => 'আফরোজা',
        'tahmina' => 'তাহমিনা', 'shahnaz' => 'শাহনাজ', 'bilkis' => 'বিলকিস', 'suraiya' => 'সুরাইয়া',
        'mahmud' => 'মাহমুদ', 'kabir' => 'কবির', 'amin' => 'আমিন', 'khalid' => 'খালিদ',
        'saif' => 'সাইফ', 'sohel' => 'সোহেল', 'hasnat' => 'হাসনাত', 'badal' => 'বাদল',
        'tariq' => 'তারেক', 'munir' => 'মুনীর', 'ashfaq' => 'আশফাক', 'quddus' => 'কুদ্দুস',
        'quader' => 'কাদের', 'kader' => 'কাদের', 'jalil' => 'জলিল', 'khalil' => 'খলিল',
        'latif' => 'লতিফ', 'rouf' => 'রউফ', 'wahab' => 'ওয়াহাব', 'baset' => 'বাসেত',
        'samad' => 'সামাদ', 'mannan' => 'মান্নান', 'hannan' => 'হান্নান', 'jabbar' => 'জব্বার',
        'sattar' => 'সাত্তার', 'gaffar' => 'গফফার', 'shafi' => 'শফি', 'shafiq' => 'শফিক',
        'shafiqul' => 'শফিকুল', 'rafiq' => 'রফিক', 'rafiqul' => 'রফিকুল', 'taufiq' => 'তৌফিক',
        'toufiq' => 'তৌফিক', 'tarique' => 'তারেক', 'firoz' => 'ফিরোজ', 'feroz' => 'ফিরোজ',
        'hafiz' => 'হাফিজ', 'hafizur' => 'হাফিজুর', 'farid' => 'ফরিদ', 'faruk' => 'ফারুক',
        'farooq' => 'ফারুক', 'anwarul' => 'আনোয়ারুল', 'azhar' => 'আজহার', 'akram' => 'আকরাম',
        'aslam' => 'আসলাম', 'azad' => 'আজাদ', 'helaluddin' => 'হেলাল উদ্দীন', 'nizam' => 'নিজাম',
        'masum' => 'মাসুম', 'kaniz' => 'কানিজ', 'zannatul' => 'জান্নাতুল', 'ferdousi' => 'ফেরদৌসী',
        'kamrun' => 'কামরুন', 'badrun' => 'বদরুন', 'khurshida' => 'খুরশীদা', 'rowshan' => 'রওশন',
        'shamsun' => 'শামসুন', 'nurjahan' => 'নূরজাহান', 'shahida' => 'শাহিদা', 'monowara' => 'মনোয়ারা',
        'lutfa' => 'লুৎফা', 'parul' => 'পারুল', 'sheuly' => 'শিউলি', 'shikha' => 'শিখা', 'rekha' => 'রেখা',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=========================================================');
        $this->info(' Starting Popular Diagnostic Doctors Import');
        $this->info(' Website: https://www.populardiagnostic.com');
        $this->info('=========================================================');

        $cacheFile = base_path('scratch/popular_doctors_raw.json');
        $rawDoctors = [];

        if (! $this->option('refresh') && file_exists($cacheFile)) {
            $this->info('Loading doctors from cached dataset: scratch/popular_doctors_raw.json...');
            $rawDoctors = json_decode(file_get_contents($cacheFile), true) ?: [];
        }

        if (empty($rawDoctors)) {
            $this->info('Harvesting doctors from Popular Diagnostic live appointment portal across 13 branches...');
            $rawDoctors = $this->harvestLiveDoctors();
            file_put_contents($cacheFile, json_encode($rawDoctors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $totalFound = count($rawDoctors);
        $this->info("Total unique Popular Diagnostic doctors available: {$totalFound}");

        $limit = $this->option('limit') ? (int) $this->option('limit') : $totalFound;
        $doctorsToProcess = array_slice($rawDoctors, 0, $limit);

        $this->info("Processing {$limit} doctors with pure Bangla translation & schedule mapping...");
        $bar = $this->output->createProgressBar(count($doctorsToProcess));
        $bar->start();

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($doctorsToProcess as $doc) {
            $popId = (int) $doc['id'];
            $engName = trim($doc['name'] ?? '');
            if (! $engName) {
                $bar->advance();

                continue;
            }

            // 1. Pure Bangla Name
            $bnName = self::toPureBanglaDoctorName($engName);

            // 2. Specialty mapping
            $specialtyName = 'মেডিসিন বিশেষজ্ঞ';
            if (! empty($doc['specialists'])) {
                foreach ($doc['specialists'] as $s) {
                    $sId = $s['specialist_id'];
                    $specObj = $this->resolveSpecialistName($sId);
                    if ($specObj && isset(self::$specialtyMap[$specObj])) {
                        $specialtyName = self::$specialtyMap[$specObj];
                        break;
                    }
                }
            }

            // 3. Designation & Title
            $bnDesignation = self::extractBanglaDesignation($engName, $doc['dr_degree'] ?? '', $doc['dr_education'] ?? '');

            // 4. Qualifications
            $qualifications = self::extractCleanQualifications($doc['dr_degree'] ?? '', $doc['dr_education'] ?? '');

            // 5. Branch & Location mapping
            $branchId = (int) ($doc['branch_id'] ?? 1);
            $primaryBranch = self::$branches[$branchId] ?? self::$branches[1];

            // Room number
            $roomNo = trim($doc['r_no'] ?? '');

            // Full Bangla Chamber Address
            $chamberAddress = "পপুলার ডায়াগনস্টিক সেন্টার ({$primaryBranch['name']} শাখা), {$primaryBranch['address']}";
            if ($roomNo) {
                $chamberAddress .= ' | রুম: '.$roomNo;
            }

            // 6. Phone & Mobile
            $phone = trim($doc['mobile'] ?? '') ?: ($doc['ast_mobile'] ?? $primaryBranch['phone']);

            // 7. Fee & Telemedicine Fee
            $fee = (float) ($doc['fee'] ?? 0);
            if ($fee <= 0) {
                $fee = (stripos($bnDesignation, 'অধ্যাপক') !== false) ? 1500 : 1000;
            }
            $teleFee = (float) ($doc['video_fee'] ?? 0) ?: round($fee * 0.8);

            // 8. Schedules & Available Days
            $availableDays = [];
            $startTime = '15:00:00';
            $endTime = '20:00:00';

            if (! empty($doc['schedules'])) {
                $days = [];
                $startTimes = [];
                $endTimes = [];
                foreach ($doc['schedules'] as $sch) {
                    if (! empty($sch['day_name'])) {
                        $days[] = ucfirst(strtolower(trim($sch['day_name'])));
                    }
                    if (! empty($sch['start_time']) && $sch['start_time'] != '00:00:00') {
                        $startTimes[] = $sch['start_time'];
                    }
                    if (! empty($sch['end_time']) && $sch['end_time'] != '00:00:00') {
                        $endTimes[] = $sch['end_time'];
                    }
                }
                if (! empty($days)) {
                    $availableDays = array_values(array_unique($days));
                }
                if (! empty($startTimes)) {
                    sort($startTimes);
                    $startTime = $startTimes[0];
                }
                if (! empty($endTimes)) {
                    rsort($endTimes);
                    $endTime = $endTimes[0];
                }
            }

            if (empty($availableDays)) {
                $availableDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
            }

            // 9. Bio in Bangla
            $bio = self::generateBanglaBio($bnName, $specialtyName, $bnDesignation, $chamberAddress, $qualifications);

            // 10. Avatar URL
            $avatar = ! empty($doc['dr_image'])
                ? ('https://www.populardiagnostic.com/'.ltrim($doc['dr_image'], '/'))
                : null;

            // 11. Slug & User Account
            $baseSlug = Str::slug($engName);
            $slug = $baseSlug.'-popular-'.$popId;
            $email = 'popular_'.$popId.'_'.Str::slug(substr($engName, 0, 15)).'@sukhimanush.com';

            // Find existing doctor by slug or popular ID
            $existing = Doctor::where('slug', $slug)
                ->orWhere(function ($q) use ($popId) {
                    $q->where('email', 'like', "popular_{$popId}_%");
                })->first();

            if ($existing) {
                $existing->update([
                    'name' => $bnName,
                    'specialization' => $specialtyName,
                    'qualifications' => $qualifications ?: $existing->qualifications,
                    'designation' => $bnDesignation,
                    'bio' => $bio,
                    'avatar' => $avatar ?: $existing->avatar,
                    'phone' => $phone ?: $existing->phone,
                    'chamber_address' => $chamberAddress,
                    'city' => $primaryBranch['city'],
                    'division' => $primaryBranch['division'],
                    'district' => $primaryBranch['district'],
                    'upazila' => $primaryBranch['upazila'],
                    'consultation_fee' => $fee,
                    'telemedicine_fee' => $teleFee,
                    'available_days' => $availableDays,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_approved' => true,
                    'is_verified' => true,
                ]);
                $updatedCount++;
            } else {
                // Create user account
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $bnName,
                        'password' => Hash::make('password123'),
                        'role' => 'doctor',
                        'phone' => $phone,
                    ]
                );

                // Experience estimation
                $exp = 10;
                if (stripos($bnDesignation, 'অধ্যাপক') !== false) {
                    $exp = 18;
                } elseif (stripos($bnDesignation, 'সহযোগী অধ্যাপক') !== false) {
                    $exp = 14;
                } elseif (stripos($bnDesignation, 'সহকারী অধ্যাপক') !== false) {
                    $exp = 10;
                }

                Doctor::create([
                    'user_id' => $user->id,
                    'name' => $bnName,
                    'slug' => $slug,
                    'specialization' => $specialtyName,
                    'qualifications' => $qualifications ?: 'MBBS',
                    'designation' => $bnDesignation,
                    'bio' => $bio,
                    'avatar' => $avatar,
                    'email' => $email,
                    'phone' => $phone,
                    'chamber_address' => $chamberAddress,
                    'city' => $primaryBranch['city'],
                    'division' => $primaryBranch['division'],
                    'district' => $primaryBranch['district'],
                    'upazila' => $primaryBranch['upazila'],
                    'consultation_fee' => $fee,
                    'telemedicine_fee' => $teleFee,
                    'experience_years' => $exp,
                    'available_days' => $availableDays,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration_minutes' => 15,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => (stripos($bnDesignation, 'অধ্যাপক') !== false),
                    'is_emergency_available' => true,
                ]);
                $insertedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=========================================================');
        $this->info(' Popular Diagnostic Import Completed Successfully!');
        $this->info(" - Newly Inserted Doctors: {$insertedCount}");
        $this->info(" - Updated Existing Doctors: {$updatedCount}");
        $this->info(' - Total Doctors in Database: '.Doctor::count());
        $this->info('=========================================================');

        return 0;
    }

    /**
     * Harvest all doctors from all 13 Popular Diagnostic branches live
     */
    protected function harvestLiveDoctors(): array
    {
        $all = [];
        foreach (self::$branches as $bId => $bData) {
            $this->line("Fetching Branch [{$bId}] {$bData['en']}...");
            try {
                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get("https://appointment.populardiagnostic.com/get-doctors/{$bId}/0/null");

                if ($response->successful()) {
                    $list = $response->json() ?: [];
                    $this->line(' -> Received '.count($list).' doctors');
                    foreach ($list as $d) {
                        if (! isset($all[$d['id']])) {
                            $all[$d['id']] = $d;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->warn("Branch {$bId} fetch error: ".$e->getMessage());
            }
        }

        return array_values($all);
    }

    /**
     * Resolve specialist name by ID from cached specialist map
     */
    protected function resolveSpecialistName(int $specialistId): ?string
    {
        static $specCache = null;
        if ($specCache === null) {
            $file = base_path('scratch/all_popular_specialties.json');
            $specCache = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
        }

        return $specCache[$specialistId] ?? null;
    }

    /**
     * Convert English doctor name to pure Bengali
     */
    public static function toPureBanglaDoctorName(string $rawName): string
    {
        // 1. Remove bracketed suffixes like (SPEC), (Junior), (Dental), (Eye), (CCU), (Tusher)
        $clean = preg_replace('/\s*\([^)]*\)/u', ' ', $rawName);
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // 2. Detect Academic & Military Titles
        $isMilitary = '';
        if (preg_match('/\b(?:brigadier\s+general|brig\.?\s*gen\.?)\b/i', $clean)) {
            $isMilitary = 'ব্রিগেডিয়ার জেনারেল (অবঃ)';
        } elseif (preg_match('/\b(?:lieutenant\s+colonel|lt\.?\s*col\.?)\b/i', $clean)) {
            $isMilitary = 'লেফটেন্যান্ট কর্নেল (অবঃ)';
        } elseif (preg_match('/\b(?:colonel|col\.?)\b/i', $clean)) {
            $isMilitary = 'কর্নেল (অবঃ)';
        } elseif (preg_match('/\b(?:major|maj\.?)\b/i', $clean)) {
            $isMilitary = 'মেজর (অবঃ)';
        }

        $isProf = preg_match('/\b(?:professor|prof\.?)\b/i', $clean);
        $isAssoc = preg_match('/\b(?:associate\s+prof(?:essor|\.)?|assoc\.?\s*prof\.?)\b/i', $clean);
        $isAsst = preg_match('/\b(?:assistant\s+prof(?:essor|\.)?|asst\.?\s*prof\.?)\b/i', $clean);

        // Strip out all title words from the name tokens
        $clean = preg_replace('/\b(?:brigadier\s+general|brig\.?\s*gen\.?|lieutenant\s+colonel|lt\.?\s*col\.?|colonel|col\.?|major|maj\.?|associate\s+prof(?:essor|\.)?|assoc\.?\s*prof\.?|assistant\s+prof(?:essor|\.)?|asst\.?\s*prof\.?|professor|prof\.?|dr\.?|doctor|retd\.?|rtd\.?)\b/i', ' ', $clean);
        $clean = preg_replace('/[().,]/', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // Determine title prefix
        if ($isMilitary && $isProf) {
            $prefix = $isMilitary.' অধ্যাপক ডাঃ ';
        } elseif ($isMilitary) {
            $prefix = $isMilitary.' ডাঃ ';
        } elseif ($isAsst) {
            $prefix = 'সহকারী অধ্যাপক ডাঃ ';
        } elseif ($isAssoc) {
            $prefix = 'সহযোগী অধ্যাপক ডাঃ ';
        } elseif ($isProf) {
            $prefix = 'অধ্যাপক ডাঃ ';
        } else {
            $prefix = 'ডাঃ ';
        }

        $tokens = preg_split('/\s+/', $clean);
        $bnTokens = [];
        foreach ($tokens as $tok) {
            $tok = trim($tok);
            if (! $tok) {
                continue;
            }
            $low = strtolower($tok);
            if (isset(self::$nameDictionary[$low])) {
                $bnTokens[] = self::$nameDictionary[$low];
            } else {
                $tr = self::transliterateToken($tok);
                if ($tr) {
                    $bnTokens[] = $tr;
                }
            }
        }

        $final = $prefix.implode(' ', $bnTokens);
        $final = preg_replace('/\bডাঃ\s+ডাঃ\b/u', 'ডাঃ', $final);
        $final = preg_replace('/\s+/', ' ', trim($final));

        return $final;
    }

    /**
     * Transliterate individual word / token
     */
    protected static function transliterateToken(string $token): string
    {
        $t = strtolower(trim($token, '.,()'));
        if (! $t) {
            return '';
        }

        // Already Bangla
        if (preg_match("/[\x{0980}-\x{09FF}]/u", $t)) {
            return $token;
        }

        if (isset(self::$nameDictionary[$t])) {
            return self::$nameDictionary[$t];
        }

        // Letter initials like M., A., MS, AHM
        if (strlen($t) <= 3 && ctype_alpha($t)) {
            $letterMap = [
                'a' => 'এ', 'b' => 'বি', 'c' => 'সি', 'd' => 'ডি', 'e' => 'ই', 'f' => 'এফ', 'g' => 'জি',
                'h' => 'এইচ', 'i' => 'আই', 'j' => 'জে', 'k' => 'কে', 'l' => 'এল', 'm' => 'এম', 'n' => 'এন',
                'o' => 'ও', 'p' => 'পি', 'q' => 'কিউ', 'r' => 'আর', 's' => 'এস', 't' => 'টি', 'u' => 'ইউ',
                'v' => 'ভি', 'w' => 'ডব্লিউ', 'x' => 'এক্স', 'y' => 'ওয়াই', 'z' => 'জেড',
            ];
            $res = [];
            for ($k = 0; $k < strlen($t); $k++) {
                $char = $t[$k];
                $res[] = $letterMap[$char] ?? $char;
            }

            return implode('.', $res).'.';
        }

        return self::transliteratePhonetic($t);
    }

    /**
     * Phonetic transliterator for unmapped Bengali words
     */
    protected static function transliteratePhonetic(string $w): string
    {
        $map = [
            'khandaker' => 'খন্দকার', 'khondker' => 'খন্দকার', 'chowdhury' => 'চৌধুরী',
            'majumder' => 'মজুমদার', 'sultana' => 'সুলতানা', 'farhana' => 'ফারহানা',
            'tanzila' => 'তানজিলা', 'tasnim' => 'তাসনিম', 'hasan' => 'হাসান',
            'hossain' => 'হোসেন', 'rahman' => 'রহমান', 'uddin' => 'উদ্দীন',
            'ullah' => 'উল্লাহ', 'uzzaman' => 'উজ্জামান', 'quddus' => 'কুদ্দুস',
            'shafiq' => 'শফিক', 'rafiq' => 'রফিক', 'taufiq' => 'তৌফিক',
            'firoz' => 'ফিরোজ', 'hafiz' => 'হাফিজ', 'parveen' => 'পারভীন',
            'parvin' => 'পারভীন', 'yasmin' => 'ইয়াসমিন', 'nasrin' => 'নাসরিন',
            'sharmin' => 'শারমিন', 'fatema' => 'ফাতেমা', 'jahan' => 'জাহান',
            'akter' => 'আক্তার', 'akhter' => 'আক্তার', 'nahar' => 'নাহার',
            'khatun' => 'খাতুন', 'begum' => 'বেগম', 'islam' => 'ইসলাম',
            'ahmed' => 'আহমেদ', 'ahmad' => 'আহমদ', 'karim' => 'করিম',
            'kamal' => 'কামাল', 'rashid' => 'রশীদ', 'zaman' => 'জামান',
            'alam' => 'আলম', 'haque' => 'হক', 'khan' => 'খান', 'ali' => 'আলী',
            'shirazi' => 'শিরাজী', 'ansari' => 'আনসারী', 'qazi' => 'কাজী',
        ];

        if (isset($map[$w])) {
            return $map[$w];
        }

        $rules = [
            'kh' => 'খ', 'gh' => 'ঘ', 'ch' => 'চ', 'chh' => 'ছ', 'jh' => 'ঝ',
            'th' => 'থ', 'dh' => 'ধ', 'ph' => 'ফ', 'bh' => 'ভ', 'sh' => 'শ',
            'k' => 'ক', 'g' => 'গ', 'j' => 'জ', 't' => 'ট', 'd' => 'দ', 'n' => 'ন',
            'p' => 'প', 'f' => 'ফ', 'b' => 'ব', 'v' => 'ভ', 'm' => 'ম', 'r' => 'র',
            'l' => 'ল', 's' => 'স', 'h' => 'হ', 'z' => 'জ', 'w' => 'ও', 'y' => 'য়',
            'ee' => 'ী', 'oo' => 'ূ', 'aa' => 'া', 'ai' => 'াই', 'oi' => 'ই',
            'a' => 'া', 'e' => 'ে', 'i' => 'ি', 'o' => 'ো', 'u' => 'ু',
        ];

        $firstVowels = [
            'a' => 'আ', 'e' => 'এ', 'i' => 'ই', 'o' => 'ও', 'u' => 'উ',
        ];

        $output = '';
        $len = strlen($w);
        $i = 0;

        while ($i < $len) {
            $sub3 = substr($w, $i, 3);
            if ($sub3 === 'chh') {
                $output .= 'ছ';
                $i += 3;

                continue;
            }

            $sub2 = substr($w, $i, 2);
            if (isset($rules[$sub2])) {
                $output .= $rules[$sub2];
                $i += 2;

                continue;
            }

            $char = $w[$i];
            if ($i === 0 && isset($firstVowels[$char])) {
                $output .= $firstVowels[$char];
                $i++;

                continue;
            }

            $output .= $rules[$char] ?? $char;
            $i++;
        }

        return $output;
    }

    /**
     * Extract clean Bangla designation
     */
    protected static function extractBanglaDesignation(string $name, string $degree, string $education): string
    {
        $text = $name.' '.$degree.' '.$education;

        if (stripos($text, 'Professor & Head') !== false || stripos($text, 'Head of Department') !== false) {
            return 'অধ্যাপক ও বিভাগীয় প্রধান';
        }
        if (stripos($text, 'Associate Professor') !== false || stripos($text, 'Assoc. Prof') !== false) {
            return 'সহযোগী অধ্যাপক';
        }
        if (stripos($text, 'Assistant Professor') !== false || stripos($text, 'Asst. Prof') !== false) {
            return 'সহকারী অধ্যাপক';
        }
        if (stripos($text, 'Professor') !== false || stripos($text, 'Prof.') !== false) {
            return 'অধ্যাপক';
        }
        if (stripos($text, 'Senior Consultant') !== false || stripos($text, 'Sr. Consultant') !== false) {
            return 'সিনিয়র কনসালট্যান্ট';
        }
        if (stripos($text, 'Consultant') !== false) {
            return 'কনসালট্যান্ট';
        }
        if (stripos($text, 'Registrar') !== false) {
            return 'রেজিস্ট্রার';
        }
        if (stripos($text, 'Specialist') !== false || stripos($text, 'Surgeon') !== false) {
            return 'বিশেষজ্ঞ চিকিৎসক ও সার্জন';
        }

        return 'বিশেষজ্ঞ চিকিৎসক';
    }

    /**
     * Clean medical qualifications
     */
    protected static function extractCleanQualifications(string $degree, string $education): string
    {
        $raw = $degree ?: $education;
        // Keep medical degrees
        $degrees = ['MBBS', 'FCPS', 'MD', 'MS', 'MRCS', 'FRCS', 'MRCP', 'FRCP', 'MACP', 'MCPS', 'DCH', 'DGO', 'DDV', 'MPhil', 'BCS', 'FCCP', 'FMAS'];
        $found = [];
        foreach ($degrees as $deg) {
            if (preg_match('/\b'.preg_quote($deg, '/').'\b/i', $raw)) {
                $found[] = $deg;
            }
        }

        return ! empty($found) ? implode(', ', $found) : 'MBBS';
    }

    /**
     * Generate natural Bangla biography
     */
    protected static function generateBanglaBio(string $name, string $specialty, string $designation, string $chamber, string $qualifications): string
    {
        return "{$name} পপুলার ডায়াগনস্টিক সেন্টারের একজন অভিজ্ঞ ও নির্ভরযোগ্য {$specialty}। তিনি {$qualifications} ডিগ্রিসম্পন্ন এবং {$designation} হিসেবে রোগীদের বিশ্বস্ততার সাথে আন্তরিক চিকিৎসাসেবা দিয়ে আসছেন। চেম্বার: {$chamber}।";
    }
}
