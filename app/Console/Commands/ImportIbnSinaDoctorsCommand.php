<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportIbnSinaDoctorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ibnsina {--limit= : Maximum number of doctors to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape and import all specialist doctors from The Ibn Sina Trust with bilingual Bangla & English information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=========================================================');
        $this->info(' Starting Ibn Sina Doctors Import with Rich Bangla Info');
        $this->info(' Website: https://www.ibnsinatrust.com');
        $this->info('=========================================================');

        $searchQueries = ['Dr', 'Prof', 'Md', 'a', 'e', 'i', 'o', 'u'];
        $rawDoctors = [];

        $this->info('Step 1: Harvesting doctors across all Ibn Sina Trust branches...');

        foreach ($searchQueries as $query) {
            $this->line("Fetching query: '{$query}'...");

            try {
                $response = Http::asForm()
                    ->timeout(45)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ])
                    ->post('https://www.ibnsinatrust.com/fetch1.php', [
                        'search' => $query,
                    ]);

                if (! $response->successful()) {
                    $this->warn("Query '{$query}' failed with status: ".$response->status());

                    continue;
                }

                $html = $response->body();

                // Match all cards
                preg_match_all("/<img[^>]*src=[\x27\"](upload\/[^\x27\"]+)[\x27\"].*?view_doctor_profile_up\.php\?id=([0-9]+)/is", $html, $matches, PREG_SET_ORDER);

                foreach ($matches as $item) {
                    $block = $item[0];
                    $ibnId = (int) $item[2];
                    $rawImg = $item[1];

                    if (isset($rawDoctors[$ibnId])) {
                        continue;
                    }

                    preg_match("/<p[^>]*style=[\x27\"][^\x27\"]*font-size:19px;[^\x27\"]*[\x27\"]>([^<]+)<\/p>/i", $block, $nameMatch);
                    preg_match("/<b>Qualifications:<\/b>\s*([^<\n]+)/i", $block, $qualMatch);
                    preg_match("/<b>\s*Specialty:\s*<\/b>\s*([^<\n]+)/i", $block, $specMatch);
                    preg_match("/<b>Designation:\s*<\/b>\s*([^<\n]+)/i", $block, $desigMatch);
                    preg_match("/<b>\s*Institute:\s*<\/b>\s*([^<\n]+)/i", $block, $instMatch);
                    preg_match("/<b>\s*Appointment:\s*<\/b>\s*([^<\n]+)/i", $block, $appMatch);
                    preg_match("/<b>\s*Chember Time:\s*<\/b>\s*([^<\n]+)/i", $block, $timeMatch);
                    preg_match("/<b>\s*Off Day:\s*<\/b>\s*([^<\n]+)/i", $block, $offMatch);
                    preg_match("/<b>\s*Floor Number:\s*<\/b>\s*([^<\n]+)/i", $block, $floorMatch);
                    preg_match("/<b>\s*Room Number:<\/b>\s*([^<\n]+)/i", $block, $roomMatch);
                    preg_match("/<b>\s*Branch Name & Address:\s*<\/b>\s*<br\s*\/?>\s*(.*?)(?:<\/p>)/is", $block, $branchMatch);

                    $name = trim($nameMatch[1] ?? '');
                    if (! $name) {
                        continue;
                    }

                    $cleanImg = 'https://www.ibnsinatrust.com/'.str_replace(' ', '%20', $rawImg);

                    $rawDoctors[$ibnId] = [
                        'ibn_id' => $ibnId,
                        'name' => $name,
                        'avatar' => $cleanImg,
                        'qualifications' => trim($qualMatch[1] ?? ''),
                        'specialty' => trim($specMatch[1] ?? ''),
                        'designation' => trim($desigMatch[1] ?? ''),
                        'institute' => trim($instMatch[1] ?? ''),
                        'phone' => trim($appMatch[1] ?? '10615'),
                        'chamber_time' => trim($timeMatch[1] ?? ''),
                        'off_day' => trim($offMatch[1] ?? ''),
                        'floor' => trim($floorMatch[1] ?? ''),
                        'room' => trim($roomMatch[1] ?? ''),
                        'branch_address' => trim(preg_replace('/\s+/', ' ', strip_tags($branchMatch[1] ?? ''))),
                    ];
                }

                $this->info('Cumulative unique doctors: '.count($rawDoctors));
            } catch (\Exception $e) {
                $this->warn("Error during '{$query}': ".$e->getMessage());
            }
        }

        $totalFound = count($rawDoctors);
        $this->info("Total unique Ibn Sina doctors harvested: {$totalFound}");

        if ($totalFound === 0) {
            $this->error('No doctors found from Ibn Sina.');

            return 1;
        }

        $limit = $this->option('limit');
        if ($limit && is_numeric($limit)) {
            $rawDoctors = array_slice($rawDoctors, 0, (int) $limit, true);
            $this->info('Limiting import to '.count($rawDoctors).' doctors as requested.');
        }

        $this->info('Step 2: Formatting Bangla info and updating database...');

        $insertedCount = 0;
        $updatedCount = 0;
        $bar = $this->output->createProgressBar(count($rawDoctors));
        $bar->start();

        foreach ($rawDoctors as $doc) {
            // Bilingual Name: ডাঃ [বাংলা নাম] ([English Name])
            $formattedName = self::formatDoctorName($doc['name']);

            // Standardized Bangla Specialization
            $bnSpecialization = self::getBanglaSpecialization($doc['specialty']);

            // Standardized Bangla Designation
            $bnDesignation = self::formatDesignation($doc['designation']);

            // Determine Location
            $addr = $doc['branch_address'];
            $district = 'Dhaka';
            $division = 'Dhaka';
            $city = 'Dhaka';
            $upazila = 'Dhanmondi';

            if (stripos($addr, 'Chattogram') !== false || stripos($addr, 'Chittagong') !== false) {
                $district = 'Chattogram';
                $division = 'Chattogram';
                $city = 'Chattogram';
                $upazila = 'Kotwali';
            } elseif (stripos($addr, 'Cumilla') !== false || stripos($addr, 'Comilla') !== false) {
                $district = 'Cumilla';
                $division = 'Chattogram';
                $city = 'Cumilla';
                $upazila = 'Cumilla Sadar';
            } elseif (stripos($addr, 'Rajshahi') !== false) {
                $district = 'Rajshahi';
                $division = 'Rajshahi';
                $city = 'Rajshahi';
                $upazila = 'Boalia';
            } elseif (stripos($addr, 'Bogura') !== false || stripos($addr, 'Bogra') !== false) {
                $district = 'Bogura';
                $division = 'Rajshahi';
                $city = 'Bogura';
                $upazila = 'Bogura Sadar';
            } elseif (stripos($addr, 'Jashore') !== false || stripos($addr, 'Jessore') !== false) {
                $district = 'Jashore';
                $division = 'Khulna';
                $city = 'Jashore';
                $upazila = 'Jashore Sadar';
            } elseif (stripos($addr, 'Noakhali') !== false) {
                $district = 'Noakhali';
                $division = 'Chattogram';
                $city = 'Maijdee';
                $upazila = 'Noakhali Sadar';
            } elseif (stripos($addr, 'Sylhet') !== false) {
                $district = 'Sylhet';
                $division = 'Sylhet';
                $city = 'Sylhet';
                $upazila = 'Sylhet Sadar';
            } elseif (stripos($addr, 'Uttara') !== false) {
                $upazila = 'Uttara';
            } elseif (stripos($addr, 'Mirpur') !== false) {
                $upazila = 'Mirpur';
            } elseif (stripos($addr, 'Badda') !== false) {
                $upazila = 'Badda';
            } elseif (stripos($addr, 'Malibagh') !== false) {
                $upazila = 'Malibagh';
            } elseif (stripos($addr, 'Savar') !== false) {
                $upazila = 'Savar';
            } elseif (stripos($addr, 'Keraniganj') !== false) {
                $upazila = 'Keraniganj';
            } elseif (stripos($addr, 'Jatrabari') !== false) {
                $upazila = 'Jatrabari';
            } elseif (stripos($addr, 'Lalbagh') !== false) {
                $upazila = 'Lalbagh';
            } elseif (stripos($addr, 'Kallyanpur') !== false) {
                $upazila = 'Kallyanpur';
            } elseif (stripos($addr, 'Zigatola') !== false) {
                $upazila = 'Zigatola';
            }

            // Clean Chamber Address with Floor & Room in Bangla
            $bnChamberAddress = self::formatChamberAddress($addr, $doc['floor'], $doc['room']);

            // Fee Estimation based on designation
            $fee = 800;
            $desig = $doc['designation'];
            if (stripos($desig, 'Professor') !== false) {
                $fee = 1200;
            } elseif (stripos($desig, 'Associate') !== false || stripos($desig, 'Assistant') !== false) {
                $fee = 1000;
            }

            // Parse Start & End Time
            $startTime = '09:00:00';
            $endTime = '17:00:00';
            $rawTime = $doc['chamber_time'];
            if (preg_match('/([0-9]{1,2}(?::[0-9]{2})?)\s*(AM|PM)?\s*[-–to]+\s*([0-9]{1,2}(?::[0-9]{2})?)\s*(AM|PM)/i', $rawTime, $tMatch)) {
                try {
                    $sStr = $tMatch[1].' '.($tMatch[2] ?: $tMatch[4]);
                    $eStr = $tMatch[3].' '.$tMatch[4];
                    $startTime = date('H:i:00', strtotime($sStr));
                    $endTime = date('H:i:00', strtotime($eStr));
                } catch (\Exception $e) {
                    // Fallback to default
                }
            }

            // Available days
            $allDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $availableDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
            if ($doc['off_day']) {
                $availableDays = array_values(array_filter($allDays, function ($day) use ($doc) {
                    return stripos($doc['off_day'], $day) === false;
                }));
                if (empty($availableDays)) {
                    $availableDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
                }
            }

            // Rich Bangla Bio
            $bnBio = self::formatBio($formattedName, $bnDesignation, $doc['institute'], $bnChamberAddress);

            // Generate unique slug
            $baseSlug = Str::slug($doc['name']);
            $slug = $baseSlug.'-ibnsina-'.$doc['ibn_id'];

            // Find existing doctor
            $existing = Doctor::where('slug', $slug)
                ->orWhere(function ($q) use ($doc, $addr) {
                    $q->where('name', 'like', "%{$doc['name']}%")
                        ->where('chamber_address', 'like', "%{$addr}%");
                })->first();

            if ($existing) {
                $existing->update([
                    'name' => $formattedName,
                    'specialization' => $bnSpecialization,
                    'qualifications' => $doc['qualifications'] ?: $existing->qualifications,
                    'designation' => $bnDesignation,
                    'bio' => $bnBio,
                    'avatar' => $doc['avatar'] ?: $existing->avatar,
                    'phone' => $doc['phone'] ?: $existing->phone,
                    'chamber_address' => $bnChamberAddress,
                    'city' => $city,
                    'division' => $division,
                    'district' => $district,
                    'upazila' => $upazila,
                    'consultation_fee' => $fee,
                    'available_days' => $availableDays,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_approved' => true,
                    'is_verified' => true,
                ]);
                $updatedCount++;
            } else {
                // Create user account for doctor
                $email = 'ibnsina_'.$doc['ibn_id'].'_'.Str::slug($doc['name']).'@sukhimanush.com';
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $formattedName,
                        'password' => Hash::make('password123'),
                        'role' => 'doctor',
                        'phone' => $doc['phone'] ?: '10615',
                    ]
                );

                Doctor::create([
                    'user_id' => $user->id,
                    'name' => $formattedName,
                    'slug' => $slug,
                    'specialization' => $bnSpecialization,
                    'qualifications' => $doc['qualifications'] ?: 'MBBS',
                    'designation' => $bnDesignation,
                    'bio' => $bnBio,
                    'avatar' => $doc['avatar'],
                    'email' => $email,
                    'phone' => $doc['phone'] ?: '10615',
                    'chamber_address' => $bnChamberAddress,
                    'city' => $city,
                    'division' => $division,
                    'district' => $district,
                    'upazila' => $upazila,
                    'consultation_fee' => $fee,
                    'telemedicine_fee' => $fee * 0.8,
                    'experience_years' => 10,
                    'available_days' => $availableDays,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration_minutes' => 15,
                    'is_approved' => true,
                    'is_verified' => true,
                    'is_featured' => (stripos($doc['designation'], 'Professor') !== false),
                    'is_emergency_available' => true,
                ]);
                $insertedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=========================================================');
        $this->info(' Import Completed Successfully!');
        $this->info(" - Newly Inserted Doctors: {$insertedCount}");
        $this->info(" - Updated Existing Doctors: {$updatedCount}");
        $this->info(' - Total Doctors in Database: '.Doctor::count());
        $this->info('=========================================================');

        return 0;
    }

    /**
     * Translates English doctor name to Bangla + English format: ডাঃ [বাংলা নাম] ([English Name])
     */
    public static function formatDoctorName(string $engName): string
    {
        $engName = trim($engName);
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $engName)) {
            return $engName;
        }

        $dictionary = [
            'dr.' => 'ডাঃ', 'dr' => 'ডাঃ', 'prof.' => 'প্রফেসর', 'prof' => 'প্রফেসর', 'professor' => 'অধ্যাপক',
            'md.' => 'মোঃ', 'md' => 'মোঃ', 'mohammad' => 'মোহাম্মদ', 'mohammed' => 'মোহাম্মদ', 'muhammad' => 'মুহাম্মদ',
            'mst.' => 'মোছাঃ', 'most.' => 'মোছাঃ', 'engr.' => 'প্রকৌঃ',
            'lt.' => 'লেঃ', 'colonel' => 'কর্নেল', 'retd' => 'অবঃ', 'retd.' => 'অবঃ',
            'brig.' => 'ব্রিগেঃ', 'gen.' => 'জেনাঃ', 'gen' => 'জেনারেল', 'rtd.' => 'অবঃ',
            'major' => 'মেজর', 'capt.' => 'ক্যাপ্টেন', 'captain' => 'ক্যাপ্টেন',

            // Common Names
            'ahmed' => 'আহমেদ', 'ahmad' => 'আহমদ', 'ali' => 'আলী', 'hasan' => 'হাসান', 'hassan' => 'হাসান',
            'hossain' => 'হোসেন', 'hussain' => 'হোসেন', 'rahman' => 'রহমান', 'khan' => 'খান', 'islam' => 'ইসলাম',
            'begum' => 'বেগম', 'khatun' => 'খাতুন', 'chowdhury' => 'চৌধুরী', 'sultana' => 'সুলতানা', 'farhana' => 'ফারহানা',
            'mahbub' => 'মাহবুব', 'mahbubul' => 'মাহবুবুল', 'alam' => 'আলম', 'kafi' => 'কাফি', 'ferdaus' => 'ফেরদৌস',
            'ferdous' => 'ফেরদৌস', 'khurshid' => 'খুরশীদ', 'shefin' => 'শেফিন', 'masuda' => 'মাসুদা', 'baqui' => 'বাকী',
            'shaheed' => 'শহীদ', 'shahid' => 'শহীদ', 'nurannabi' => 'নূরুন্নবী', 'shah' => 'শাহ', 'mostafa' => 'মোস্তফা',
            'mustafa' => 'মুস্তফা', 'kamal' => 'কামাল', 'rashid' => 'রশীদ', 'karim' => 'করিম', 'zaman' => 'জামান',
            'haque' => 'হক', 'tarafdar' => 'তরফদার', 'talukdar' => 'তালুকদার', 'majumder' => 'মজুমদার', 'saha' => 'সাহা',
            'ghosh' => 'ঘোষ', 'roy' => 'রায়', 'das' => 'দাস', 'dutta' => 'দত্ত', 'sen' => 'সেন', 'barua' => 'বড়ুয়া',
            'debnath' => 'দেবনাথ', 'paul' => 'পাল', 'pal' => 'পাল', 'bhowmik' => 'ভৌমিক', 'biswas' => 'বিশ্বাস',
            'bishwas' => 'বিশ্বাস', 'sikder' => 'সিকদার', 'mollah' => 'মোল্লা', 'gazi' => 'গাজী', 'mia' => 'মিয়া',
            'miah' => 'মিয়া', 'akter' => 'আক্তার', 'akhter' => 'আক্তার', 'nahar' => 'নাহার', 'nasrin' => 'নাসরিন',
            'sharmin' => 'শারমিন', 'ruma' => 'রুমা', 'shirin' => 'শিরিন', 'fatema' => 'ফাতেমা', 'jahan' => 'জাহান',
            'parveen' => 'পারভীন', 'parvin' => 'পারভীন', 'ranu' => 'রানু', 'laila' => 'লায়লা', 'salma' => 'সালমা',
            'rokeya' => 'রোকেয়া', 'yasmin' => 'ইয়াসমিন', 'monir' => 'মনির', 'moniruzzaman' => 'মনিরুজ্জামান',
            'ashraf' => 'আশরাফ', 'anisur' => 'আনিসুর', 'anis' => 'আনিস', 'motiur' => 'মতিউর', 'mizanur' => 'মিজানুর',
            'lutfor' => 'লুৎফর', 'lutfur' => 'লুৎফর', 'reza' => 'রেজা', 'sayeed' => 'সাঈদ', 'saeed' => 'সাঈদ',
            'shahidul' => 'শহীদুল', 'sirajul' => 'সিরাজুল', 'nazrul' => 'নজরুল', 'enamul' => 'এনামুল', 'ziaur' => 'জিয়াউর',
            'habibur' => 'হাবিবুর', 'saiful' => 'সাইফুল', 'tariqul' => 'তরিকুল', 'zahid' => 'জাহিদ', 'rashed' => 'রাশেদ',
            'faisal' => 'ফয়সাল', 'tanvir' => 'তানভীর', 'imran' => 'ইমরান', 'arif' => 'আরিফ', 'tareq' => 'তারেক',
            'shohel' => 'সোহেল', 'sumon' => 'সুমন', 'shakil' => 'শাকিল', 'rubel' => 'রুবেল', 'shimul' => 'শিমুল',
            'ripon' => 'রিপন', 'shuvo' => 'শুভ', 'sabbir' => 'সাব্বির', 'nayeem' => 'নাঈম', 'tamim' => 'তামিম',
            'rifat' => 'রিফাত', 'siam' => 'সিয়াম', 'arafat' => 'আরাফাত', 'riaz' => 'রিয়াজ', 'mirza' => 'মির্জা',
            'syed' => 'সৈয়দ', 'sheikh' => 'শেখ', 'kazi' => 'কাজী', 'sardar' => 'সরদার', 'bhuiyan' => 'ভূঁইয়া',
            'munshi' => 'মুন্সী', 'khandaker' => 'খন্দকার', 'khondker' => 'খন্দকার', 'anwar' => 'আনোয়ার', 'golam' => 'গোলাম',
            'shamsul' => 'শামসুল', 'fazlul' => 'ফজলুল', 'asif' => 'আসিফ', 'adnan' => 'আদনান', 'shahin' => 'শাহীন',
            'sohag' => 'সোহাগ', 'mehedi' => 'মেহেদী', 'nazmul' => 'নাজমুল', 'ashik' => 'আশিক', 'sakib' => 'সাকিব',
            'noman' => 'নোমান', 'helal' => 'হেলাল', 'habib' => 'হাবিব', 'harun' => 'হারুন', 'selim' => 'সেলিম',
            'salim' => 'সালিম', 'mamun' => 'মামুন', 'monjur' => 'মঞ্জুর', 'manzur' => 'মনজুর', 'jahangir' => 'জাহাঙ্গীর',
            'alamgir' => 'আলমগীর', 'jahidul' => 'জাহিদুল', 'zakir' => 'জাকির', 'zobaida' => 'জুবাইদা', 'tasnim' => 'তাসনিম',
            'tanzila' => 'তানজিলা', 'sadia' => 'সাদিয়া', 'sabrina' => 'সাবরিনা', 'nadia' => 'নাদিয়া', 'nusrat' => 'নুসরাত',
            'nargis' => 'নার্গিস', 'samina' => 'সামিনা', 'farzana' => 'ফারজানা', 'shahana' => 'শাহানা', 'rebeka' => 'রেবেকা',
            'afroza' => 'আফরোজা', 'tahmina' => 'তাহমিনা', 'shahnaz' => 'শাহনাজ', 'bilkis' => 'বিলকিস', 'suraiya' => 'সুরাইয়া',
        ];

        $tokens = preg_split('/\s+/', $engName);
        $bnTokens = [];

        foreach ($tokens as $token) {
            $clean = strtolower(trim($token, '.,()'));
            if (isset($dictionary[$clean])) {
                $bnTokens[] = $dictionary[$clean];
            } else {
                $bnTokens[] = $token;
            }
        }

        $bnName = implode(' ', $bnTokens);
        if (! str_contains($bnName, 'ডাঃ') && ! str_contains($bnName, 'প্রফেসর') && ! str_contains($bnName, 'অধ্যাপক')) {
            $bnName = 'ডাঃ '.$bnName;
        }

        return "{$bnName} ({$engName})";
    }

    /**
     * Translates specialty to standardized Bengali categories
     */
    public static function getBanglaSpecialization(string $spec): string
    {
        $s = strtolower($spec);
        if (strpos($s, 'cardio') !== false || strpos($s, 'heart') !== false) {
            return 'হৃদরোগ ও কার্ডিওলজি (Cardiology)';
        }
        if (strpos($s, 'pediatric') !== false || strpos($s, 'child') !== false || strpos($s, 'neonat') !== false) {
            return 'শিশু ও কিশোর রোগ (Pediatrics)';
        }
        if (strpos($s, 'gynae') !== false || strpos($s, 'obs') !== false || strpos($s, 'female') !== false) {
            return 'গাইনী ও প্রসূতিরোগ (Gynecology & Obs)';
        }
        if (strpos($s, 'ortho') !== false || strpos($s, 'bone') !== false || strpos($s, 'spine') !== false) {
            return 'অর্থোপেডিক ও হাড়-জোড়া (Orthopedics)';
        }
        if (strpos($s, 'skin') !== false || strpos($s, 'derma') !== false || strpos($s, 'vd') !== false || strpos($s, 'allergy') !== false) {
            return 'চর্ম, যৌন ও অ্যালার্জি (Dermatology)';
        }
        if (strpos($s, 'neuro') !== false || strpos($s, 'brain') !== false) {
            return 'নিউরোলজি ও ব্রেন (Neurology)';
        }
        if (strpos($s, 'nephro') !== false || strpos($s, 'kidney') !== false || strpos($s, 'uro') !== false) {
            return 'কিডনি ও ইউরোলজি (Urology & Kidney)';
        }
        if (preg_match('/\b(ent|otolaryngology)\b/i', $s) || strpos($s, 'ear') !== false || strpos($s, 'throat') !== false || strpos($s, 'nose') !== false) {
            return 'নাক, কান ও গলা (ENT)';
        }
        if (strpos($s, 'gastro') !== false || strpos($s, 'liver') !== false || strpos($s, 'hepato') !== false) {
            return 'গ্যাস্ট্রোএন্টারোলজি ও লিভার (Gastroenterology)';
        }
        if (strpos($s, 'hormone') !== false || strpos($s, 'diabet') !== false || strpos($s, 'endocrin') !== false || strpos($s, 'thyroid') !== false) {
            return 'ডায়াবেটিস, হরমোন ও থাইরয়েড (Endocrinology)';
        }
        if (strpos($s, 'chest') !== false || strpos($s, 'pulmon') !== false || strpos($s, 'respirat') !== false) {
            return 'বক্ষব্যাধি ও ফুসফুস (Pulmonology)';
        }
        if (strpos($s, 'psychiat') !== false || strpos($s, 'mental') !== false) {
            return 'মানসিক রোগ ও মনোরোগ (Psychiatry)';
        }
        if (strpos($s, 'dental') !== false || strpos($s, 'tooth') !== false) {
            return 'ডেন্টাল ও মুখরোগ (Dental Care)';
        }
        if (strpos($s, 'eye') !== false || strpos($s, 'ophthal') !== false) {
            return 'চক্ষু বিশেষজ্ঞ (Ophthalmology)';
        }
        if (strpos($s, 'cancer') !== false || strpos($s, 'onco') !== false) {
            return 'ক্যান্সার বিশেষজ্ঞ (Oncology)';
        }
        if (strpos($s, 'hemat') !== false || strpos($s, 'blood') !== false) {
            return 'রক্তরোগ বিশেষজ্ঞ (Hematology)';
        }
        if (strpos($s, 'surg') !== false) {
            return 'জেনারেল ও ল্যাপারোস্কপিক সার্জারি (Surgery)';
        }

        return 'মেডিসিন বিশেষজ্ঞ (General Medicine)';
    }

    /**
     * Translates designation to standard Bangla designation
     */
    public static function formatDesignation(string $desig): string
    {
        $d = strtolower($desig);
        if (strpos($d, 'head') !== false) {
            if (strpos($d, 'prof') !== false) {
                return 'অধ্যাপক ও বিভাগীয় প্রধান (Professor & Head of Dept)';
            }

            return 'বিভাগীয় প্রধান ও কনসালটেন্ট (Head of Department)';
        }
        if (strpos($d, 'prof') !== false) {
            return 'অধ্যাপক (Professor)';
        }
        if (strpos($d, 'assoc') !== false) {
            return 'সহযোগী অধ্যাপক (Associate Professor)';
        }
        if (strpos($d, 'assist') !== false) {
            return 'সহকারী অধ্যাপক (Assistant Professor)';
        }
        if (strpos($d, 'senior') !== false) {
            return 'সিনিয়র কনসালটেন্ট (Senior Consultant)';
        }
        if (strpos($d, 'consult') !== false) {
            return 'কনসালটেন্ট (Consultant)';
        }
        if (strpos($d, 'regist') !== false) {
            return 'রেজিস্ট্রার (Registrar)';
        }

        return 'স্পেশালিস্ট কনসালটেন্ট (Specialist Consultant)';
    }

    /**
     * Translates Ibn Sina Chamber address to standard Bangla
     */
    public static function formatChamberAddress(string $addr, string $floor = '', string $room = ''): string
    {
        $branchMap = [
            'Dhanmondi' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড ইমেজিং সেন্টার, বাড়ি ৪৮, রোড ৯/এ, ধানমন্ডি, ঢাকা-১২০৯',
            'Specialized Hospital, Dhanmondi' => 'ইবনে সিনা স্পেশালাইজড হাসপাতাল, বাড়ি ৬৮, রোড ১৫, ধানমন্ডি, ঢাকা',
            'Zigatola' => 'ইবনে সিনা মেডিকেল ইমেজিং সেন্টার, বাড়ি ৫৮, রোড ৩/এ, জিগাতলা, ঢাকা',
            'Kallyanpur' => 'ইবনে সিনা মেডিকেল কলেজ হাসপাতাল, ১/১-বি, কল্যাণপুর, ঢাকা',
            'Mirpur' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, প্লট ১১, ব্লক ই, সেকশন ৬, মিরপুর, ঢাকা',
            'Doyagonj' => 'ইবনে সিনা ডি.ল্যাব অ্যান্ড কনসালটেশন সেন্টার, ২৮ দয়াগঞ্জ, ঢাকা',
            'Badda' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, চ-৭২/১, প্রগতি সরণি, উত্তর বাড্ডা, ঢাকা',
            'uttara' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, বাড়ি ৫২, গরিব-ই-নেওয়াজ এভিনিউ, সেক্টর ১৩, উত্তরা, ঢাকা',
            'Lalbagh' => 'ইবনে সিনা ডায়াগনস্টিক সেন্টার, ২৬/১ শরৎ গুপ্ত রোড, লালবাগ, ঢাকা',
            'Jashore' => 'ইবনে সিনা হাসপাতাল অ্যান্ড ডায়াগনস্টিক সেন্টার, জেল রোড, যশোর',
            'Jessore' => 'ইবনে সিনা হাসপাতাল অ্যান্ড ডায়াগনস্টিক সেন্টার, জেল রোড, যশোর',
            'Bogura' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, কানুচগাড়ি, শেরপুর রোড, বগুড়া',
            'Bogra' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, কানুচগাড়ি, শেরপুর রোড, বগুড়া',
            'Malibagh' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, হাউজ ৪৭২, ডিআইটি রোড, মালিবাগ, ঢাকা',
            'Savar' => 'ইবনে সিনা হাসপাতাল অ্যান্ড ডায়াগনস্টিক সেন্টার, থানা রোড, সাভার',
            'Keraniganj' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, কদমতলী, কেরানীগঞ্জ',
            'Chattogram' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, হাউজ ১৬, রোড ২, ও.আর. নিজাম রোড, চট্টগ্রাম',
            'Cumilla' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, কান্দিরপাড়, কুমিল্লা',
            'Rajshahi' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, বর্ণালী মোড়, গ্রেটার রোড, রাজশাহী',
            'Jatrabari' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, শহীদ ফারুক সড়ক, উত্তর যাত্রাবাড়ী, ঢাকা',
            'Noakhali' => 'ইবনে সিনা হাসপাতাল অ্যান্ড ডায়াগনস্টিক সেন্টার, মাইজদী কোর্ট রোড, নোয়াখালী',
            'Arshinagar' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, আরশিনগর, ঢাকা',
            'Cancer' => 'ইবনে সিনা ক্যান্সার ডায়াগনস্টিক সেন্টার, হাউজ ৪৭, রোড ১০/এ, ধানমন্ডি, ঢাকা',
        ];

        $bnBase = '';
        foreach ($branchMap as $keyword => $bnText) {
            if (stripos($addr, $keyword) !== false) {
                $bnBase = $bnText;
                break;
            }
        }

        if (! $bnBase) {
            $bnBase = $addr;
        }

        if ($floor) {
            $bnBase .= ', '.$floor.' তলা';
        }
        if ($room) {
            $bnBase .= ', রুম নং: '.$room;
        }

        return $bnBase;
    }

    /**
     * Generates a patient-friendly Bangla bio
     */
    public static function formatBio(string $name, string $desig, string $institute, string $branch): string
    {
        $bio = "{$name}, {$desig}। ";
        if ($institute) {
            $bio .= "কর্মস্থল: {$institute}। ";
        }
        $bio .= "বর্তমানে {$branch} চেম্বারে নিয়মিত রোগী দেখছেন ও বিশেষজ্ঞ পরামর্শ দিচ্ছেন।";

        return $bio;
    }
}
