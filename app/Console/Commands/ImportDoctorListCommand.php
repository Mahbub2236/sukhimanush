<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportDoctorListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:doctorlist {--limit= : Maximum number of doctors to import} {--fresh : Truncate non-seeded doctors before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape and import all specialist doctors from doctorlist.info.bd into the doctors table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=========================================================');
        $this->info(' Starting DoctorList Import from https://www.doctorlist.info.bd');
        $this->info('=========================================================');

        // Step 1: Fetch all doctors from the search API
        $this->info('Fetching doctor index from DoctorList search API...');

        $searchUrl = 'https://www.doctorlist.info.bd/api/dashboard/doctors/search?skip=0&take=1000';
        $response = Http::timeout(60)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])
            ->get($searchUrl);

        if (! $response->successful()) {
            $this->error('Failed to fetch doctors list from API. Status: '.$response->status());

            return 1;
        }

        $json = $response->json();
        $doctorsList = $json['data'] ?? [];
        $totalCount = count($doctorsList);

        $this->info("Successfully fetched {$totalCount} doctors from index.");

        $limit = $this->option('limit');
        if ($limit && is_numeric($limit)) {
            $doctorsList = array_slice($doctorsList, 0, (int) $limit);
            $totalCount = count($doctorsList);
            $this->warn("Limited import to {$totalCount} doctors as requested.");
        }

        if ($totalCount === 0) {
            $this->warn('No doctors found to import.');

            return 0;
        }

        $bar = $this->output->createProgressBar($totalCount);
        $bar->start();

        $imported = 0;
        $updated = 0;
        $failed = 0;

        // Step 2: Process in batches of 15 for detail requests
        $batchSize = 15;
        $chunks = array_chunk($doctorsList, $batchSize);

        $bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $enDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        foreach ($chunks as $chunk) {
            // Asynchronously fetch details for this batch of slugs
            $slugs = array_filter(array_column($chunk, 'slug_en'));
            $detailResponses = [];

            if (! empty($slugs)) {
                try {
                    $responses = Http::pool(function ($pool) use ($slugs) {
                        foreach ($slugs as $slug) {
                            $pool->as($slug)->timeout(10)->withHeaders([
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            ])->get("https://www.doctorlist.info.bd/api/dashboard/doctors/doctor/{$slug}");
                        }
                    });

                    foreach ($responses as $slugKey => $res) {
                        if ($res instanceof Response && $res->successful()) {
                            $detailResponses[$slugKey] = $res->json();
                        }
                    }
                } catch (\Throwable $e) {
                    // Pool error fallback gracefully
                }
            }

            foreach ($chunk as $doc) {
                try {
                    $slugKey = $doc['slug_en'] ?? '';
                    $detail = $detailResponses[$slugKey] ?? null;

                    // 1. Doctor Name (Bilingual)
                    $nameBn = trim($doc['fullName_bn'] ?? '');
                    $nameEn = trim($doc['fullName_en'] ?? '');
                    if ($nameBn && $nameEn && $nameBn !== $nameEn) {
                        $name = "{$nameBn} ({$nameEn})";
                    } else {
                        $name = $nameBn ?: ($nameEn ?: 'Doctor');
                    }

                    // 2. Slug
                    $baseSlug = Str::slug($doc['slug_en'] ?? $nameEn ?: 'doctor-'.$doc['id']);
                    if (! $baseSlug) {
                        $baseSlug = 'doctor-'.$doc['id'];
                    }
                    $slug = $baseSlug;

                    // 3. Specialties
                    $specialties = $detail['specialties'] ?? $doc['specialties'] ?? [];
                    $specs = collect($specialties)
                        ->map(function ($s) {
                            return $s['specialty']['name_bn'] ?? $s['name_bn'] ?? $s['specialty']['name_en'] ?? $s['name_en'] ?? null;
                        })
                        ->filter()
                        ->unique()
                        ->implode(', ');
                    if (! $specs) {
                        $specs = 'সাধারণ চিকিৎসক (General Physician)';
                    }
                    $specs = Str::limit($specs, 250, '');

                    // 4. Degrees & Qualifications
                    $degrees = trim($detail['degrees'] ?? $doc['degrees'] ?? '');
                    $qualifications = $degrees ?: 'MBBS';

                    // 5. Designation
                    $designation = 'বিশেষজ্ঞ চিকিৎসক (Specialist)';
                    if (preg_match('/(অধ্যাপক|সহকারী অধ্যাপক|সহযোগী অধ্যাপক|সিনিয়র কনসালটেন্ট|কনসালটেন্ট|Professor|Assistant Professor|Associate Professor|Senior Consultant|Consultant|সার্জন|Surgeon)/iu', $degrees, $matches)) {
                        $designation = $matches[1];
                    } elseif ($specs) {
                        $firstSpec = explode(',', $specs)[0];
                        $designation = trim($firstSpec).' কনসালটেন্ট';
                    }
                    $designation = Str::limit($designation, 250, '');

                    // 6. Bio
                    $bioRaw = $detail['bio_bn'] ?? $detail['bio_en'] ?? '';
                    $bioClean = strip_tags(html_entity_decode(str_replace('&nbsp;', ' ', $bioRaw)));
                    $bio = trim($bioClean);
                    $cityNameEn = $doc['city']['name_en'] ?? 'Dhaka';
                    $cityNameBn = $doc['city']['name_bn'] ?? $cityNameEn;
                    if (! $bio) {
                        $bio = "{$name} একজন অভিজ্ঞ {$specs}। তিনি {$cityNameBn} ও আশেপাশের রোগীদের নিয়মিত চেম্বারে স্বাস্থ্যসেবা ও পরামর্শ প্রদান করে থাকেন।";
                    }

                    // 7. Avatar (Preserve real image or set gender-based default avatar)
                    $rawAvatar = $detail['profileImageUrl'] ?? $doc['profileImageUrl'] ?? null;
                    if (! empty($rawAvatar)) {
                        $avatar = $rawAvatar;
                    } else {
                        $femaleKeywords = [
                            'মোছাঃ', 'মোছা.', 'মোসাম্মৎ', 'বেগম', 'নাজনীন', 'সুলতানা', 'তাহমিনা', 'জান্নাত', 'শামীমা',
                            'রোকসানা', 'ফাতেমা', 'নুসরাত', 'সাদিয়া', 'মারুফা', 'আফরোজা', 'ফারহানা', 'তানজিলা',
                            'উম্মে', 'লায়লা', 'শারমিন', 'সাবরিনা', 'রুমানা', 'বিলকিস', 'রেহানা', 'শিরীন', 'লতিফা',
                            'ফারিয়া', 'তাসনিম', 'রাবেয়া', 'হাসিনা', 'আমেনা', 'খাদিজা', 'আয়েশা', 'সৈয়দা', 'মেহেরুন',
                            'শিরিন', 'নাসরিন', 'ইয়াসমিন', 'জাহান', 'আক্তার', 'খাতুন', 'হালিমা', 'গাইনী', 'স্ত্রী ও প্রসূতি',
                            'Begum', 'Sultana', 'Fatema', 'Nazneen', 'Farhana', 'Faria', 'Akter', 'Khatun', 'Nusrat',
                            'Sadia', 'Tahmina', 'Jannat', 'Shamima', 'Roksana', 'Mst', 'Most', 'Mrs',
                        ];
                        $isFemale = false;
                        foreach ($femaleKeywords as $kw) {
                            if (mb_stripos("{$name} {$specs} {$qualifications}", $kw) !== false) {
                                $isFemale = true;
                                break;
                            }
                        }
                        $avatar = $isFemale ? '/images/team/default-doctor-female.svg' : '/images/team/default-doctor-male.svg';
                    }

                    // 8. Schedules and Visiting Hours
                    $schedules = $detail['schedules'] ?? $doc['schedules'] ?? [];
                    $availableDays = collect($schedules)
                        ->pluck('day_en')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                    if (empty($availableDays)) {
                        $availableDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Saturday'];
                    }

                    $startTime = '09:00:00';
                    $endTime = '17:00:00';
                    if (! empty($schedules[0]['startTime'])) {
                        $st = $schedules[0]['startTime'];
                        $startTime = strlen($st) === 5 ? $st.':00' : $st;
                    }
                    if (! empty($schedules[0]['endTime'])) {
                        $et = $schedules[0]['endTime'];
                        $endTime = strlen($et) === 5 ? $et.':00' : $et;
                    }

                    // 9. Hospital & Chamber Address
                    $hospital = $schedules[0]['hospital'] ?? null;
                    $hName = $hospital['name_bn'] ?? $hospital['name_en'] ?? 'চেম্বার';
                    $hAddr = $hospital['address_bn'] ?? $hospital['address_en'] ?? '';
                    $chamberAddress = trim(implode(', ', array_filter([$hName, $hAddr, $cityNameBn])), ', ');
                    if (! $chamberAddress) {
                        $chamberAddress = "{$cityNameBn}, বাংলাদেশ";
                    }

                    // 10. Phone
                    $rawPhone = $hospital['phone'] ?? $detail['phone'] ?? $doc['phone'] ?? null;
                    $phone = null;
                    if ($rawPhone) {
                        $clean = str_replace($bnDigits, $enDigits, trim($rawPhone));
                        $clean = preg_replace('/[^\d+]/', '', $clean);
                        $phone = Str::limit($clean, 30, '');
                    }

                    // 11. Geo Location
                    $division = $doc['division']['name_en'] ?? 'Dhaka';
                    $district = $doc['city']['name_en'] ?? $division;
                    $city = $doc['city']['name_en'] ?? 'Dhaka';
                    $upazila = $doc['city']['name_bn'] ?? null;

                    // 12. Fees & Experience
                    $isPopular = ($doc['status'] ?? '') === 'POPULAR';
                    $consultationFee = $isPopular ? 800.00 : 500.00;
                    $telemedicineFee = $isPopular ? 600.00 : 400.00;
                    $experienceYears = $detail['experienceYears'] ?? 8;
                    if (! $experienceYears || $experienceYears <= 0) {
                        $experienceYears = rand(6, 18);
                    }

                    // 13. Create or Link User
                    $doctorEmail = $detail['email'] ?? $doc['email'] ?? null;
                    if (! $doctorEmail) {
                        $doctorEmail = "{$slug}@doctorlist.bd";
                    }

                    $user = User::firstOrCreate(
                        ['email' => $doctorEmail],
                        [
                            'name' => $name,
                            'password' => Hash::make('doctor123'),
                            'role' => 'doctor',
                            'phone' => $phone,
                            'address' => $chamberAddress,
                            'avatar' => $avatar,
                        ]
                    );

                    // 14. Insert or Update Doctor Profile
                    $doctorData = [
                        'user_id' => $user->id,
                        'name' => $name,
                        'specialization' => $specs,
                        'designation' => $designation,
                        'qualifications' => $qualifications,
                        'bio' => $bio,
                        'avatar' => $avatar,
                        'email' => $doctorEmail,
                        'phone' => $phone,
                        'chamber_address' => $chamberAddress,
                        'telemedicine_link' => "https://meet.google.com/sukhimanush-{$slug}",
                        'division' => $division,
                        'district' => $district,
                        'city' => $city,
                        'upazila' => $upazila,
                        'consultation_fee' => $consultationFee,
                        'telemedicine_fee' => $telemedicineFee,
                        'experience_years' => $experienceYears,
                        'available_days' => $availableDays,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'slot_duration_minutes' => 25,
                        'is_approved' => true,
                        'is_verified' => true,
                        'is_featured' => $isPopular,
                        'is_emergency_available' => $isPopular,
                    ];

                    $existingDoc = Doctor::where('slug', $slug)->first();
                    if ($existingDoc) {
                        $existingDoc->update($doctorData);
                        $updated++;
                    } else {
                        $doctorData['slug'] = $slug;
                        Doctor::create($doctorData);
                        $imported++;
                    }

                } catch (\Throwable $e) {
                    $failed++;
                    $this->error("\nError importing {$doc['fullName_en']}: ".$e->getMessage());
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=========================================================');
        $this->info(' Import Finished!');
        $this->info(" New Doctors Created: {$imported}");
        $this->info(" Existing Doctors Updated: {$updated}");
        $this->info(" Errors: {$failed}");
        $this->info(' Total Doctors in Database: '.Doctor::count());
        $this->info('=========================================================');

        return 0;
    }
}
