<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SmsNotificationService
{
    /**
     * Send booking confirmation SMS to patient.
     */
    public function sendBookingConfirmation(Appointment $appointment): array
    {
        $doctorName = $appointment->doctor ? $appointment->doctor->name : 'ডাক্তার';
        $patientName = $appointment->patient_name;
        $serial = $appointment->serial_number;
        $date = $appointment->appointment_date ? (is_string($appointment->appointment_date) ? $appointment->appointment_date : $appointment->appointment_date->format('d M, Y')) : 'নির্ধারিত তারিখ';
        $time = $appointment->appointment_time ?: 'চেম্বার সময়সূচী অনুযায়ী';
        $trackingCode = $appointment->appointment_number;
        $phone = $appointment->patient_phone;
        $chamberName = $appointment->chamber ? ($appointment->chamber->chamber_name.($appointment->chamber->branch_name ? ' - '.$appointment->chamber->branch_name : '')) : ($appointment->doctor?->chamber_address ?: 'চেম্বার');

        if ($appointment->consultation_type === 'online_video') {
            $chamberUrl = url("/video-chamber/{$trackingCode}");
            $message = "প্রিয় {$patientName}, ডাঃ {$doctorName}-এর সাথে আপনার অনলাইন ভিডিও কনসালটেশন নিশ্চিত হয়েছে। সিরিয়াল নং #{$serial}। তারিখ: {$date}, সময়: {$time}। কোনো অ্যাপ ছাড়া সরাসরি ১-ক্লিকে ভিডিও কলে কথা বলতে লিংকে চাপ দিন: {$chamberUrl} - সুখী মানুষ";
        } else {
            $message = "প্রিয় {$patientName}, ডাঃ {$doctorName}-এর চেম্বারে ({$chamberName}) আপনার সিরিয়াল নং #{$serial} নিশ্চিত হয়েছে। তারিখ: {$date}, সময়: {$time}। ট্র্যাকিং কোড: {$trackingCode}। লাইভ সিরিয়াল দেখতে ভিজিট করুন: ".url("/track-appointment?query={$trackingCode}").' - সুখী মানুষ';
        }

        $smsStatus = $this->sendRaw($phone, $message);
        $whatsappUrl = $this->getWhatsAppShareUrl($appointment);

        return [
            'sms_status' => $smsStatus,
            'whatsapp_url' => $whatsappUrl,
            'message' => $message,
        ];
    }

    /**
     * Send alert when patient's turn is called in chamber.
     */
    public function sendQueueCallAlert(Appointment $appointment): array
    {
        $doctorName = $appointment->doctor ? $appointment->doctor->name : 'ডাক্তার';
        $patientName = $appointment->patient_name;
        $serial = $appointment->serial_number;
        $phone = $appointment->patient_phone;

        $message = "জরুরি অ্যালার্ট: সিরিয়াল নং #{$serial} ({$patientName}), ডাঃ {$doctorName} আপনাকে চেম্বারে ডেকেছেন। অনুগ্রহ করে অবিলম্বে ডাক্তারের রুমে প্রবেশ করুন। - সুখী মানুষ";

        $smsStatus = $this->sendRaw($phone, $message);

        return [
            'sms_status' => $smsStatus,
            'message' => $message,
        ];
    }

    /**
     * Send alert when patient's turn is upcoming (e.g. 2-3 patients left).
     */
    public function sendUpcomingQueueAlert(Appointment $appointment, int $remainingCount): array
    {
        $doctorName = $appointment->doctor ? $appointment->doctor->name : 'ডাক্তার';
        $patientName = $appointment->patient_name;
        $serial = $appointment->serial_number;
        $phone = $appointment->patient_phone;

        $message = "সিরিয়াল অ্যালার্ট: জনাব {$patientName}, ডাঃ {$doctorName}-এর চেম্বারে আপনার সিরিয়াল #{$serial}। আর মাত্র {$remainingCount} জন রোগী পর আপনার পালা। অনুগ্রহ করে চেম্বারের ওয়েটিং রুমে অবস্থান করুন। - সুখী মানুষ";

        $smsStatus = $this->sendRaw($phone, $message);

        return [
            'sms_status' => $smsStatus,
            'message' => $message,
        ];
    }

    /**
     * Generate 1-Click WhatsApp Direct Share URL for Bangladesh.
     */
    public function getWhatsAppShareUrl(Appointment $appointment): string
    {
        $doctorName = $appointment->doctor ? $appointment->doctor->name : 'বিশেষজ্ঞ ডাক্তার';
        $doctorDept = $appointment->doctor ? ($appointment->doctor->specialization ?: 'চেম্বার') : 'মেডিকেল';
        $chamber = $appointment->chamber
            ? ($appointment->chamber->chamber_name.($appointment->chamber->branch_name ? " ({$appointment->chamber->branch_name})" : '').', '.$appointment->chamber->address)
            : ($appointment->doctor ? ($appointment->doctor->chamber_address ?: 'হাসপাতাল চেম্বার') : 'চেম্বার');
        $patientName = $appointment->patient_name;
        $serial = $appointment->serial_number;
        $date = $appointment->appointment_date ? (is_string($appointment->appointment_date) ? $appointment->appointment_date : $appointment->appointment_date->format('d M, Y')) : '';
        $time = $appointment->appointment_time ?: '';
        $code = $appointment->appointment_number;
        $trackUrl = url("/track-appointment?query={$code}");

        $isOnlineVideo = ($appointment->consultation_type === 'online_video');
        $videoChamberUrl = url("/video-chamber/{$code}");

        if ($isOnlineVideo) {
            $waText = "*🏥 সুখী মানুষ (Sukhi Manush) - অনলাইন ভিডিও কনসালটেশন পাস*\n"
                ."━━━━━━━━━━━━━━━━━━\n"
                ."👤 *রোগীর নাম:* {$patientName}\n"
                ."🔢 *সিরিয়াল নম্বর:* #{$serial}\n"
                ."👨‍⚕️ *ডাক্তার:* ডাঃ {$doctorName} ({$doctorDept})\n"
                ."🎥 *কনসালটেশন মোড:* অনলাইন লাইভ ভিডিও চেম্বার\n"
                ."📅 *তারিখ:* {$date}\n"
                ."⏰ *সময়:* {$time}\n"
                ."🔖 *ট্র্যাকিং কোড:* {$code}\n"
                ."━━━━━━━━━━━━━━━━━━\n"
                ."📹 *১-ক্লিকে ভিডিও কলে কথা বলতে এখানে চাপ দিন:* \n{$videoChamberUrl}\n\n"
                ."📱 *লাইভ সিরিয়াল ট্র্যাকিং:* \n{$trackUrl}\n\n"
                .'_(কোনো অ্যাপ বা ইমেইল লাগবে না, সরাসরি ব্রাউজারেই ভিডিও ও অডিও কথা বলা যাবে)_';
        } else {
            $waText = "*🏥 সুখী মানুষ (Sukhi Manush) - ডিজিটাল অ্যাপয়েন্টমেন্ট পাস*\n"
                ."━━━━━━━━━━━━━━━━━━\n"
                ."👤 *রোগীর নাম:* {$patientName}\n"
                ."🔢 *সিরিয়াল নম্বর:* #{$serial}\n"
                ."👨‍⚕️ *ডাক্তার:* ডাঃ {$doctorName} ({$doctorDept})\n"
                ."📍 *চেম্বার:* {$chamber}\n"
                ."📅 *তারিখ:* {$date}\n"
                ."⏰ *সময়:* {$time}\n"
                ."🔖 *ট্র্যাকিং কোড:* {$code}\n"
                ."━━━━━━━━━━━━━━━━━━\n"
                ."📱 *লাইভ সিরিয়াল ট্র্যাকিং ও স্লিপ ডাউনলোড:* \n{$trackUrl}\n\n"
                .'_(চেম্বারে সিরিয়াল ডাকার সময় সতর্ক থাকুন এবং সময়মতো উপস্থিত থাকুন)_';
        }

        $cleanPhone = $this->normalizePhoneNumber($appointment->patient_phone, true);

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text=".urlencode($waText);
    }

    /**
     * Send raw SMS via configured gateway (Greenweb, BulkSMSBD, AlphaSMS, or Log fallback).
     */
    public function sendRaw(string $phone, string $message): array
    {
        $recipient = $this->normalizePhoneNumber($phone, false);
        $rateKey = 'sms_rate_limit:'.$recipient;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            Log::warning('[SMS Abuse Protection]: Rate limit exceeded for recipient');

            return [
                'success' => false,
                'mode' => 'throttled',
                'message' => 'Too many SMS messages sent to this recipient. Please wait a few minutes.',
            ];
        }

        RateLimiter::hit($rateKey, 600); // 10 minute window

        $driver = config('services.sms.driver', 'log');
        $apiKey = config('services.sms.api_key', '');
        $senderId = config('services.sms.sender_id', 'SukhiManush');

        // If no credentials or driver is log, safely log and return success
        if (empty($apiKey) || $driver === 'log') {
            $maskedPhone = substr($recipient, 0, 4).'****'.substr($recipient, -3);
            Log::info("[Sukhi Manush SMS Gateway Mock] To: {$maskedPhone}");

            return [
                'success' => true,
                'mode' => 'log_mock',
                'recipient' => $recipient,
                'note' => 'SMS logged safely. Configure SMS_API_KEY in .env for live telecom broadcast.',
            ];
        }

        try {
            if ($driver === 'greenweb') {
                // Greenweb SMS Gateway API
                $response = Http::timeout(10)->post('http://api.greenweb.com.bd/api.php', [
                    'token' => $apiKey,
                    'to' => $recipient,
                    'message' => $message,
                ]);

                return [
                    'success' => $response->successful(),
                    'gateway' => 'greenweb',
                    'response' => $response->body(),
                ];
            } elseif ($driver === 'bulksmsbd') {
                // BulkSMSBD Gateway API
                $username = config('services.sms.username', '');
                $password = config('services.sms.password', '');

                $response = Http::timeout(10)->post('http://bulksmsbd.net/api/smsapi', [
                    'api_key' => $apiKey,
                    'type' => 'text',
                    'number' => $recipient,
                    'senderid' => $senderId,
                    'message' => $message,
                ]);

                return [
                    'success' => $response->successful(),
                    'gateway' => 'bulksmsbd',
                    'response' => $response->body(),
                ];
            } else {
                // Generic Custom API Endpoint
                $customUrl = config('services.sms.custom_url', '');
                if (! empty($customUrl)) {
                    $response = Http::timeout(10)->post($customUrl, [
                        'api_key' => $apiKey,
                        'recipient' => $recipient,
                        'sender_id' => $senderId,
                        'message' => $message,
                    ]);

                    return [
                        'success' => $response->successful(),
                        'gateway' => 'custom',
                        'response' => $response->body(),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('[Sukhi Manush SMS Dispatch Error]: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        return ['success' => true, 'mode' => 'log'];
    }

    /**
     * Clean and normalize Bangladesh phone numbers.
     * e.g. "01712345678" -> "8801712345678" (for WhatsApp) or "01712345678"
     */
    public function normalizePhoneNumber(string $phone, bool $withCountryCode = true): string
    {
        // Strip non-digits
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 880, strip it if we don't want country code
        if (str_starts_with($cleaned, '880')) {
            $local = substr($cleaned, 2);
        } else {
            $local = $cleaned;
        }

        // Ensure 0 at start for local
        if (! str_starts_with($local, '0')) {
            $local = '0'.$local;
        }

        if ($withCountryCode) {
            return '88'.$local;
        }

        return $local;
    }
}
