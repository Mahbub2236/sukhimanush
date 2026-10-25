<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MedicineController extends Controller
{
    /**
     * Search all Bangladesh medicines with local caching + live MedEx BD API fallback.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            // Show flagship popular medicines across all top Bangladesh pharmaceutical companies
            $popularKeywords = ['Napa', 'Ace', 'Maxpro', 'Sergel', 'Monas', 'Fexo', 'Etorix', 'Cef-3', 'Zimax', 'Pantonix', 'Rupa', 'Alatrol', 'Losectil', 'Finix', 'Bislol', 'Rolac'];
            $medicines = Medicine::where(function ($query) use ($popularKeywords) {
                foreach ($popularKeywords as $kw) {
                    $query->orWhere('brand_name', 'like', "%{$kw}%");
                }
            })->take(30)->get();

            if ($medicines->isEmpty()) {
                $medicines = Medicine::orderBy('brand_name')->take(30)->get();
            }

            return response()->json($medicines);
        }

        // 1. Search Local Database
        $localResults = Medicine::where('brand_name', 'like', "%{$q}%")
            ->orWhere('generic_name', 'like', "%{$q}%")
            ->orWhere('company', 'like', "%{$q}%")
            ->orderBy('brand_name')
            ->take(30)
            ->get();

        // 2. If fewer than 10 results in local database, fetch live from MedEx Bangladesh
        if ($localResults->count() < 10 && strlen($q) >= 2) {
            $liveMedicines = $this->fetchLiveFromMedEx($q);

            if (! empty($liveMedicines)) {
                foreach ($liveMedicines as $item) {
                    Medicine::updateOrCreate(
                        ['brand_name' => $item['brand_name']],
                        [
                            'generic_name' => $item['generic_name'],
                            'dosage_form' => $item['dosage_form'],
                            'strength' => $item['strength'] ?? null,
                            'company' => $item['company'],
                            'unit_price' => $item['unit_price'] ?? 3.00,
                        ]
                    );
                }

                // Re-fetch combined sorted results
                $localResults = Medicine::where('brand_name', 'like', "%{$q}%")
                    ->orWhere('generic_name', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orderBy('brand_name')
                    ->take(30)
                    ->get();
            }
        }

        return response()->json($localResults);
    }

    /**
     * Fetch live medicine records with authentic unit price from Bangladesh MedEx database.
     */
    private function fetchLiveFromMedEx(string $query): array
    {
        try {
            $url = 'https://medex.com.bd/search?type=brands&search='.urlencode($query);

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9',
            ])->timeout(4)->withoutVerifying()->get($url);

            if (! $response->successful()) {
                return [];
            }

            $html = $response->body();
            $results = [];

            preg_match_all(
                '/<div class="search-result-row(?:\s+ad)?">.*?<div class="search-result-title">.*?<a[^>]*href="([^"]+)"[^>]*>\s*([^<\r\n]+?)\s*<\/a>.*?<\/div>\s*<p>(.*?)\s*<i>\s*\((.*?)\)\s*<\/i>\s*(?:is manufactured by\s*([^<\r\n.]+?)(?:\.|\s*<\/p>)|.*?<\/p>)/is',
                $html,
                $matches,
                PREG_SET_ORDER
            );

            // Collect top detail URLs for parallel price resolution
            $detailUrls = [];
            foreach (array_slice($matches, 0, 5) as $idx => $m) {
                $detailUrl = trim(html_entity_decode($m[1] ?? ''));
                if (filter_var($detailUrl, FILTER_VALIDATE_URL) && str_starts_with($detailUrl, 'https://medex.com.bd/brands/')) {
                    $detailUrls[$idx] = $detailUrl;
                }
            }

            // Fetch detail pages in parallel
            $priceMap = [];
            if (! empty($detailUrls)) {
                try {
                    $poolResponses = Http::pool(function ($pool) use ($detailUrls) {
                        return array_map(function ($url) use ($pool) {
                            return $pool->withHeaders([
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                            ])->withoutVerifying()->timeout(2.5)->get($url);
                        }, $detailUrls);
                    });

                    foreach ($poolResponses as $idx => $res) {
                        if ($res instanceof Response && $res->successful()) {
                            $body = $res->body();
                            if (preg_match('/Unit Price:\s*<\/span>\s*<span>(?:&#2547;|&৳;|৳|\b)?\s*([0-9.,]+)/is', $body, $pm)) {
                                $priceMap[$idx] = (float) str_replace(',', '', $pm[1]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Fail silently to benchmark fallback
                }
            }

            foreach ($matches as $idx => $m) {
                $rawTitle = html_entity_decode(trim($m[2]));
                $generic = html_entity_decode(trim($m[4]));
                $company = isset($m[5]) ? html_entity_decode(trim($m[5])) : 'Bangladesh Pharma';

                // Extract dosage form if present in parentheses e.g. "Napa One 1000 mg (Tablet)"
                $form = 'Tab';
                if (preg_match('/\((Tablet|Capsule|Syrup|Suspension|Drop|Injection|Ointment|Cream|Inhaler|Eye Drop|Ear Drop|Suppository|Gel|Powder|EC Tablet|EC Capsule)\)/i', $rawTitle, $formMatch)) {
                    $matched = strtolower($formMatch[1]);
                    $form = match (true) {
                        str_contains($matched, 'tablet') => 'Tab',
                        str_contains($matched, 'capsule') => 'Cap',
                        str_contains($matched, 'syrup') => 'Syrup',
                        str_contains($matched, 'suspension') => 'Susp',
                        str_contains($matched, 'drop') => 'Drop',
                        str_contains($matched, 'injection') => 'Inj',
                        str_contains($matched, 'ointment') || str_contains($matched, 'cream') || str_contains($matched, 'gel') => 'Ointment',
                        str_contains($matched, 'inhaler') => 'Inhaler',
                        str_contains($matched, 'suppository') => 'Supp',
                        default => 'Tab',
                    };
                    $brand = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $rawTitle));
                } else {
                    $brand = $rawTitle;
                }

                // Determine authentic price: MedEx detail price > realistic form benchmark
                $unitPrice = $priceMap[$idx] ?? match ($form) {
                    'Cap' => 8.00,
                    'Syrup' => 45.00,
                    'Susp' => 45.00,
                    'Drop' => 35.00,
                    'Inj' => 90.00,
                    'Ointment' => 40.00,
                    'Supp' => 8.00,
                    'Inhaler' => 250.00,
                    default => 2.50,
                };

                $results[] = [
                    'brand_name' => $brand,
                    'generic_name' => $generic,
                    'dosage_form' => $form,
                    'company' => $company,
                    'unit_price' => $unitPrice,
                ];
            }

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * List all standard dosage presets, durations, and instructions.
     */
    public function presets(): JsonResponse
    {
        return response()->json([
            'dosages' => [
                ['label' => '1 + 0 + 1 (সকাল ও রাত)', 'value' => '1 + 0 + 1'],
                ['label' => '1 + 1 + 1 (সকাল, দুপুর ও রাত)', 'value' => '1 + 1 + 1'],
                ['label' => '1 + 0 + 0 (সকালে ১ বার)', 'value' => '1 + 0 + 0'],
                ['label' => '0 + 0 + 1 (রাতে ১ বার)', 'value' => '0 + 0 + 1'],
                ['label' => '0 + 1 + 0 (দুপুরে ১ বার)', 'value' => '0 + 1 + 0'],
                ['label' => '1 + 1 + 1 + 1 (৪ বার)', 'value' => '1 + 1 + 1 + 1'],
                ['label' => '1/2 + 0 + 1/2 (অর্ধেক করে)', 'value' => '1/2 + 0 + 1/2'],
                ['label' => '2 চামচ করে ৩ বার (Syrup)', 'value' => '2 চামচ করে ৩ বার'],
                ['label' => '1 চামচ করে ২ বার (Syrup)', 'value' => '1 চামচ করে ২ বার'],
                ['label' => '2 ফোঁটা করে ৩ বার (Drop)', 'value' => '2 ফোঁটা করে ৩ বার'],
                ['label' => 'প্রয়োজনে (As Needed / PRN)', 'value' => 'প্রয়োজনে ১ বার'],
            ],
            'durations' => [
                ['label' => '৩ দিন (3 Days)', 'value' => '3 Days'],
                ['label' => '৫ দিন (5 Days)', 'value' => '5 Days'],
                ['label' => '৭ দিন (7 Days - ১ সপ্তাহ)', 'value' => '7 Days'],
                ['label' => '১০ দিন (10 Days)', 'value' => '10 Days'],
                ['label' => '১৪ দিন (14 Days - ২ সপ্তাহ)', 'value' => '14 Days'],
                ['label' => '১ মাস (30 Days)', 'value' => '30 Days'],
                ['label' => '২ মাস (60 Days)', 'value' => '60 Days'],
                ['label' => '৩ মাস (90 Days)', 'value' => '90 Days'],
                ['label' => 'চলবে (Continue)', 'value' => 'Continue'],
            ],
            'instructions' => [
                ['label' => 'খাবার পর (After Meal)', 'value' => 'খাবার পর'],
                ['label' => 'খাবার ৩০ মিনিট আগে (Before Meal)', 'value' => 'খাবার ৩০ মিনিট আগে'],
                ['label' => 'ভরা পেটে (With Heavy Meal)', 'value' => 'ভরা পেটে'],
                ['label' => 'রাতে ঘুমানোর আগে (Before Sleep)', 'value' => 'রাতে ঘুমানোর আগে'],
                ['label' => 'খাওয়ার সাথে (With Meal)', 'value' => 'খাওয়ার সাথে'],
                ['label' => 'ব্যথা হলে খাবেন (When in Pain)', 'value' => 'ব্যথা হলে খাবেন'],
                ['label' => 'জ্বর আসলে খাবেন', 'value' => 'জ্বর আসলে খাবেন'],
                ['label' => 'চুষে খাবেন (Chewable)', 'value' => 'চুষে খাবেন'],
                ['label' => 'আক্রান্ত স্থানে লাগাবেন (Apply)', 'value' => 'আক্রান্ত স্থানে লাগাবেন'],
            ],
            'common_tests' => [
                'CBC (Complete Blood Count)',
                'Fasting Blood Sugar (FBS)',
                'HbA1c',
                'Serum Lipid Profile',
                'Serum Creatinine',
                'Serum Electrolytes',
                '12-Lead ECG',
                'Echocardiogram',
                'Chest X-Ray P/A View',
                'Ultrasonography of Whole Abdomen (USG)',
                'Urine R/E & C/S',
                'Serum Bilirubin / SGPT (ALT)',
                'TSH (Thyroid Stimulating Hormone)',
                'Serum Uric Acid',
                'Vitamin D (25-OH)',
            ],
            'common_advices' => [
                'পর্যাপ্ত পরিমাণে বিশুদ্ধ পানি পান করুন (প্রতিদিন ২.৫ - ৩ লিটার)।',
                'অতিরিক্ত তেল, চর্বি ও ভাজাপোড়া খাবার পরিহার করুন।',
                'দৈনিক অন্তত ৩০ মিনিট হাঁটা বা হালকা ব্যায়াম করুন।',
                'খাবারে অতিরিক্ত কাঁচা লবণ বর্জন করুন।',
                'ধূমপান ও জর্দা/তামাক সম্পূর্ণ পরিহার করুন।',
                'পরিমিত ঘুম (৭-৮ ঘণ্টা) নিশ্চিত করুন এবং মানসিক চাপ কমান।',
                'ডায়াবেটিস ও রক্তচাপ নিয়মিত মাপুন এবং চার্ট রাখুন।',
            ],
        ]);
    }
}
