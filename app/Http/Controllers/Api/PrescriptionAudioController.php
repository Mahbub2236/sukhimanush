<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PrescriptionAudioController extends Controller
{
    /**
     * Generate & stream studio-quality Human Bengali Voice MP3 for prescription.
     */
    public function getAudio(int $id, Request $request)
    {
        $prescription = Prescription::with(['doctor', 'appointment'])->findOrFail($id);

        $authUser = $request->user('sanctum');
        $isAdmin = $authUser && in_array($authUser->role ?? '', ['admin', 'super_admin'], true);
        $isDoctor = $authUser && $prescription->doctor && ($authUser->id === $prescription->doctor->user_id);
        $isPatient = $authUser && ($authUser->id === $prescription->patient_id);

        // Controlled verification for patients without direct Sanctum session
        $hasTokenAccess = false;
        if ($request->filled('rx_number') && $request->input('rx_number') === $prescription->prescription_number) {
            $hasTokenAccess = true;
        } elseif ($request->filled('phone') && $prescription->appointment) {
            $patientPhoneClean = substr(preg_replace('/[^0-9]/', '', (string) $prescription->appointment->patient_phone), -10);
            $inputPhoneClean = substr(preg_replace('/[^0-9]/', '', (string) $request->input('phone')), -10);
            if (! empty($patientPhoneClean) && $patientPhoneClean === $inputPhoneClean) {
                $hasTokenAccess = true;
            }
        }

        if (! $isAdmin && ! $isDoctor && ! $isPatient && ! $hasTokenAccess) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাক্সেস! প্রেসক্রিপশনের অডিও শোনার অনুমতি নেই। (Unauthorized: Medical Data Privacy Protection)',
            ], 403);
        }

        $audioDir = storage_path('app/public/audio');
        if (! File::exists($audioDir)) {
            File::makeDirectory($audioDir, 0755, true);
        }

        $filePath = $audioDir."/rx_{$id}.mp3";

        // If cached audio exists, stream it directly
        if (File::exists($filePath) && File::size($filePath) > 1000) {
            return response()->file($filePath, [
                'Content-Type' => 'audio/mpeg',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // Build natural, warm human Bengali script
        $script = $this->buildBanglaScript($prescription);

        // Split into chunks under 180 characters for Google TTS
        $chunks = $this->splitIntoChunks($script, 180);

        $fullAudioData = '';
        foreach ($chunks as $chunk) {
            $chunkAudio = $this->fetchGoogleTTSChunk(trim($chunk));
            if ($chunkAudio) {
                $fullAudioData .= $chunkAudio;
            }
        }

        if (empty($fullAudioData)) {
            return response()->json(['success' => false, 'message' => 'অডিও তৈরি করা যায়নি'], 500);
        }

        // Cache the combined MP3 file
        File::put($filePath, $fullAudioData);

        return response()->file($filePath, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Build natural human Bengali script from prescription data.
     */
    private function buildBanglaScript(Prescription $rx): string
    {
        $docName = $rx->doctor ? $rx->doctor->name : 'বিশেষজ্ঞ ডাক্তার';
        $cleanDocName = trim(explode('(', $docName)[0]);

        $diagnosis = $rx->diagnosis ? trim(explode('(', $rx->diagnosis)[0]) : '';
        $diagnosis = str_replace([',', 'and', '&'], [' ও ', ' ও ', ' ও '], $diagnosis);

        $script = "আসসালামু আলাইকুম। {$cleanDocName} আপনার প্রেসক্রিপশন প্রস্তুত করেছেন। ";

        if (! empty($diagnosis)) {
            $script .= "আপনার শারীরিক সমস্যা: {$diagnosis}। ";
        }

        $script .= 'ওষুধের নিয়মাবলী মনোযোগ দিয়ে শুনুন। ';

        $medicines = $rx->medicines;
        if (is_string($medicines)) {
            $medicines = json_decode($medicines, true) ?: [];
        }

        if (! empty($medicines)) {
            foreach ($medicines as $idx => $m) {
                $medNum = $this->numberToBanglaWord($idx + 1);
                $cleanMedName = $this->cleanMedicineName($m['name'] ?? '');
                $dosageBn = $this->normalizeDosage($m['dosage'] ?? '');
                $instructionBn = $this->normalizeInstruction($m['instruction'] ?? '');
                $durationBn = $this->normalizeDuration($m['duration'] ?? '');

                $script .= "{$medNum} নম্বর ওষুধ: {$cleanMedName}। মাত্রা: {$dosageBn}। নিয়ম: {$instructionBn}। সময়কাল: {$durationBn}। ";
            }
        }

        if (! empty($rx->advice)) {
            $cleanAdvice = trim(explode('(', $rx->advice)[0]);
            $script .= "ডাক্তারের বিশেষ পরামর্শ: {$cleanAdvice}। ";
        }

        $script .= 'ওষুধ নিয়মিত সেবন করুন এবং সুস্থ থাকুন। ধন্যবাদ।';

        return $script;
    }

    /**
     * Clean medicine names (remove parentheses generics and expand Tab/Cap).
     */
    private function cleanMedicineName(string $name): string
    {
        $name = trim(preg_replace('/\s*\([^)]*\)/', '', $name)); // Remove (Generics)
        $name = preg_replace('/^Tab\.?\s*/i', 'ট্যাবলেট ', $name);
        $name = preg_replace('/^Cap\.?\s*/i', 'ক্যাপসুল ', $name);
        $name = preg_replace('/^Syr\.?\s*/i', 'সিরাপ ', $name);
        $name = preg_replace('/^Inj\.?\s*/i', 'ইনজেকশন ', $name);
        $name = preg_replace('/(\d+)\s*mg/i', '$1 মিলিগ্রাম', $name);
        $name = preg_replace('/(\d+)\s*ml/i', '$1 মিলি', $name);

        return $name;
    }

    /**
     * Convert dosage string (e.g. 1 + 0 + 0, 1+0+1) to spoken Bengali.
     */
    private function normalizeDosage(string $dosage): string
    {
        $clean = strtolower(preg_replace('/\s+/', '', $dosage));

        $map = [
            '1+0+0' => 'সকালে একটি',
            '0+1+0' => 'দুপুরে একটি',
            '0+0+1' => 'রাতে একটি',
            '1+0+1' => 'সকালে একটি এবং রাতে একটি',
            '1+1+0' => 'সকালে একটি এবং দুপুরে একটি',
            '0+1+1' => 'দুপুরে একটি এবং রাতে একটি',
            '1+1+1' => 'সকালে একটি, দুপুরে একটি এবং রাতে একটি',
            '1/2+0+1/2' => 'সকালে অর্ধেক এবং রাতে অর্ধেক',
            '1/2+1/2+1/2' => 'সকালে অর্ধেক, দুপুরে অর্ধেক এবং রাতে অর্ধেক',
            '1+0+0+1' => 'সকালে একটি এবং রাতে শোবার আগে একটি',
            '2+0+2' => 'সকালে দুইটি এবং রাতে দুইটি',
        ];

        return $map[$clean] ?? "প্রতিবারে {$dosage}";
    }

    /**
     * Normalize instructions.
     */
    private function normalizeInstruction(string $inst): string
    {
        $lower = strtolower($inst);
        if (str_contains($lower, 'after') || str_contains($lower, 'পর')) {
            return 'খাবার খাওয়ার পরে সেব্য';
        }
        if (str_contains($lower, 'before') || str_contains($lower, 'আগে')) {
            return 'খাবার খাওয়ার আগে খালি পেটে সেব্য';
        }
        if (str_contains($lower, 'bedtime') || str_contains($lower, 'ঘুমানো')) {
            return 'রাতে ঘুমানোর পূর্বে সেব্য';
        }

        return ! empty($inst) ? $inst : 'খাবার খাওয়ার পর সেব্য';
    }

    /**
     * Normalize duration.
     */
    private function normalizeDuration(string $dur): string
    {
        if (empty($dur)) {
            return 'ডাক্তারের পরামর্শ অনুযায়ী';
        }

        $dur = preg_replace('/(\d+)\s*days?/i', '$1 দিন যাবত', $dur);
        $dur = preg_replace('/(\d+)\s*weeks?/i', '$1 সপ্তাহ', $dur);
        $dur = preg_replace('/(\d+)\s*months?/i', '$1 মাস', $dur);
        $dur = preg_replace('/continue/i', 'নিয়মিত চলবে', $dur);

        return $dur;
    }

    /**
     * Number to Bengali word.
     */
    private function numberToBanglaWord(int $n): string
    {
        $words = [1 => 'এক', 2 => 'দুই', 3 => 'তিন', 4 => 'চার', 5 => 'পাঁচ', 6 => 'ছয়', 7 => 'সাত', 8 => 'আট', 9 => 'নয়', 10 => 'দশ'];

        return $words[$n] ?? (string) $n;
    }

    /**
     * Split text into speech-friendly sentences/chunks.
     */
    private function splitIntoChunks(string $text, int $maxLen): array
    {
        $parts = preg_split('/([।\.!\?]+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sentences = [];
        for ($i = 0; $i < count($parts); $i += 2) {
            $sentence = trim(($parts[$i] ?? '').($parts[$i + 1] ?? ''));
            if (! empty($sentence)) {
                $sentences[] = $sentence;
            }
        }

        $chunks = [];
        $current = '';

        foreach ($sentences as $s) {
            if (mb_strlen($current.' '.$s) <= $maxLen) {
                $current = trim($current.' '.$s);
            } else {
                if (! empty($current)) {
                    $chunks[] = $current;
                }
                $current = $s;
            }
        }

        if (! empty($current)) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    /**
     * Fetch MP3 audio chunk from Google Bengali TTS.
     */
    private function fetchGoogleTTSChunk(string $text): ?string
    {
        $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=bn&client=tw-ob&q='.urlencode($text);

        try {
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://translate.google.com/',
            ])->timeout(12)->get($url);

            if ($res->successful()) {
                return $res->body();
            }
        } catch (\Exception $e) {
            // Fallback or retry
        }

        return null;
    }
}
