<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Models\Payment;
use App\Services\SmsNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentWebhookController extends Controller
{
    /**
     * Handle bKash Automated Webhook / IPN Notification.
     * Supports bKash Tokenized Checkout & Merchant Webhooks.
     * URL: POST /api/payments/webhook/bkash
     */
    /**
     * Verify authenticity of incoming webhook if webhook secret is configured.
     */
    private function validateWebhookSecurity(Request $request, string $gateway): bool
    {
        // Official SSLCommerz IPN verification via server-to-server validation API
        if ($gateway === 'sslcommerz' && $request->filled('val_id') && config('services.sslcommerz.store_id') && config('services.sslcommerz.store_passwd')) {
            return true;
        }

        $configuredSecret = config('services.'.$gateway.'.webhook_secret')
            ?: config('services.'.$gateway.'.secret')
            ?: config('services.payment.webhook_secret');

        if (! $configuredSecret) {
            Log::error("[Payment Webhook Security Alert]: Webhook secret not configured for {$gateway}. Request blocked.");

            return false;
        }

        $receivedSecret = $request->header('X-Webhook-Secret')
            ?: $request->header('X-Signature')
            ?: $request->input('webhook_secret')
            ?: $request->input('signature');

        return ! empty($receivedSecret) && hash_equals((string) $configuredSecret, (string) $receivedSecret);
    }

    public function handleBkashWebhook(Request $request): JsonResponse
    {
        if (! $this->validateWebhookSecurity($request, 'bkash')) {
            Log::warning('[bKash Webhook Security Warning]: Rejected request due to invalid secret/signature from IP: '.$request->ip());

            return response()->json(['success' => false, 'message' => 'Unauthorized webhook invocation.'], 403);
        }

        $payload = $request->all();
        $trxId = $payload['trxID'] ?? $payload['transaction_id'] ?? null;
        $appointmentNumber = $payload['merchantInvoiceNumber'] ?? $payload['invoiceNumber'] ?? $payload['order_id'] ?? null;
        Log::info('[bKash Webhook Notification Received]: Invoice: '.($appointmentNumber ?: 'N/A').', TrxID: '.($trxId ?: 'N/A'));

        // bKash payload standard keys
        $trxId = $payload['trxID'] ?? $payload['transaction_id'] ?? null;
        $appointmentNumber = $payload['merchantInvoiceNumber'] ?? $payload['invoiceNumber'] ?? $payload['order_id'] ?? null;
        $status = strtoupper($payload['transactionStatus'] ?? $payload['status'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);

        if (! $appointmentNumber && isset($payload['paymentID'])) {
            // Attempt to resolve by transaction or payment ID if mapped
            $existingPayment = Payment::where('transaction_id', $payload['paymentID'])->first();
            if ($existingPayment) {
                $appointmentNumber = $existingPayment->appointment?->appointment_number;
            }
        }

        if (! $appointmentNumber) {
            Log::warning('[bKash Webhook Failed]: Missing merchant invoice / appointment number.', $payload);

            return response()->json([
                'success' => false,
                'message' => 'Invoice or appointment number missing from webhook payload.',
            ], 400);
        }

        $appointment = Appointment::with(['doctor', 'chamber', 'familyMember'])
            ->where('appointment_number', trim($appointmentNumber))
            ->first();

        if (! $appointment) {
            Log::warning("[bKash Webhook]: Appointment #{$appointmentNumber} not found.");

            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        }

        // Idempotency: Check if already processed
        if ($appointment->payment_status === 'paid') {
            Log::info("[bKash Webhook]: Appointment #{$appointmentNumber} was already marked as PAID.");

            return response()->json([
                'success' => true,
                'message' => 'Payment already completed previously.',
            ], 200);
        }

        // Check if bKash payment status is Completed
        if ($status === 'COMPLETED' || $status === 'SUCCESS' || $status === 'AUTHORIZED') {
            $currency = strtoupper($payload['currency'] ?? 'BDT');
            if ($currency !== 'BDT') {
                Log::error("[bKash Webhook Fraud Alert]: Currency mismatch for #{$appointmentNumber}. Expected: BDT, Received: {$currency}");

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid currency. Only BDT is accepted.',
                ], 422);
            }

            $requiredFee = (float) $appointment->consultation_fee;

            // Strict Amount Verification (Anti-Fraud / Underpayment check)
            if ($amount > 0 && $amount < $requiredFee) {
                Log::error("[bKash Webhook Fraud Alert]: Underpayment detected for #{$appointmentNumber}. Required: {$requiredFee}, Received: {$amount}");

                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount is less than the required consultation fee.',
                ], 422);
            }

            $fee = $amount > 0 ? $amount : $requiredFee;
            $adminCommission = round($fee * 0.10, 2);
            $doctorEarning = round($fee - $adminCommission, 2);
            $finalTrxId = $trxId ?: ('BKASH-'.strtoupper(Str::random(10)));

            // Replay Attack Prevention: verify that transaction_id is not already used for another appointment
            $existingTrx = Payment::where('transaction_id', $finalTrxId)->first();
            if ($existingTrx && $existingTrx->appointment_id !== $appointment->id) {
                Log::error("[bKash Webhook Security Alert]: Replay attack detected. TrxID {$finalTrxId} already used for appointment #{$existingTrx->appointment_id}");

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID has already been utilized.',
                ], 409);
            }

            // Atomic Multi-Step Database Write
            DB::transaction(function () use ($appointment, $finalTrxId, $fee, $adminCommission, $doctorEarning) {
                // 1. Mark Appointment as Paid and Confirmed
                $appointment->payment_status = 'paid';
                $appointment->payment_method = 'bkash';
                $appointment->status = 'confirmed';
                $appointment->save();

                // 2. Record or Update Transaction in payments table
                Payment::updateOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'transaction_id' => $finalTrxId,
                        'doctor_id' => $appointment->doctor_id,
                        'patient_id' => $appointment->patient_id,
                        'amount' => $fee,
                        'admin_commission' => $adminCommission,
                        'doctor_earning' => $doctorEarning,
                        'payment_method' => 'bkash',
                        'status' => 'completed',
                    ]
                );
            });

            // 3. Dispatch automated Bangla SMS and WhatsApp Confirmation
            try {
                app(SmsNotificationService::class)->sendBookingConfirmation($appointment);
            } catch (\Exception $e) {
                Log::error('[bKash Webhook SMS Dispatch Error]: '.$e->getMessage());
            }

            Log::info("[bKash Webhook Success]: Appointment #{$appointmentNumber} confirmed with TrxID: {$finalTrxId}");

            return response()->json([
                'success' => true,
                'message' => 'bKash payment verified and appointment confirmed successfully.',
                'appointment_number' => $appointment->appointment_number,
                'trx_id' => $finalTrxId,
            ], 200);
        }

        Log::warning("[bKash Webhook]: Payment not completed. Status received: {$status}");

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => 'Transaction status is not completed.',
        ], 200);
    }

    /**
     * Handle Nagad Automated IPN / Webhook Notification.
     * URL: POST /api/payments/webhook/nagad
     */
    public function handleNagadWebhook(Request $request): JsonResponse
    {
        if (! $this->validateWebhookSecurity($request, 'nagad')) {
            Log::warning('[Nagad Webhook Security Warning]: Rejected request due to invalid secret/signature from IP: '.$request->ip());

            return response()->json(['success' => false, 'message' => 'Unauthorized webhook invocation.'], 403);
        }

        $payload = $request->all();
        $orderId = $payload['order_id'] ?? $payload['merchant_order_id'] ?? null;
        $paymentRef = $payload['payment_ref_id'] ?? $payload['issuer_payment_ref'] ?? null;
        Log::info('[Nagad Webhook Notification Received]: Order ID: '.($orderId ?: 'N/A').', Ref: '.($paymentRef ?: 'N/A'));
        $status = strtoupper($payload['status'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);

        if (! $orderId) {
            return response()->json(['success' => false, 'message' => 'Order ID missing.'], 400);
        }

        $appointment = Appointment::with(['doctor', 'chamber'])
            ->where('appointment_number', trim($orderId))
            ->first();

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        if ($appointment->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Already paid.'], 200);
        }

        if ($status === 'SUCCESS' || $status === 'COMPLETED') {
            $currency = strtoupper($payload['currency'] ?? 'BDT');
            if ($currency !== 'BDT') {
                Log::error("[Nagad Webhook Fraud Alert]: Currency mismatch for #{$orderId}. Expected: BDT, Received: {$currency}");

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid currency. Only BDT is accepted.',
                ], 422);
            }

            $requiredFee = (float) $appointment->consultation_fee;

            // Strict Amount Verification (Anti-Fraud / Underpayment check)
            if ($amount > 0 && $amount < $requiredFee) {
                Log::error("[Nagad Webhook Fraud Alert]: Underpayment detected for #{$orderId}. Required: {$requiredFee}, Received: {$amount}");

                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount is less than the required consultation fee.',
                ], 422);
            }

            $fee = $amount > 0 ? $amount : $requiredFee;
            $adminCommission = round($fee * 0.10, 2);
            $doctorEarning = round($fee - $adminCommission, 2);
            $finalTrxId = $paymentRef ?: ('NAGAD-'.strtoupper(Str::random(10)));

            // Replay Attack Prevention: verify transaction_id is not already used elsewhere
            $existingTrx = Payment::where('transaction_id', $finalTrxId)->first();
            if ($existingTrx && $existingTrx->appointment_id !== $appointment->id) {
                Log::error("[Nagad Webhook Security Alert]: Replay attack detected. TrxID {$finalTrxId} already used for appointment #{$existingTrx->appointment_id}");

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID has already been utilized.',
                ], 409);
            }

            // Atomic Multi-Step Database Write
            DB::transaction(function () use ($appointment, $finalTrxId, $fee, $adminCommission, $doctorEarning) {
                $appointment->payment_status = 'paid';
                $appointment->payment_method = 'nagad';
                $appointment->status = 'confirmed';
                $appointment->save();

                Payment::updateOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'transaction_id' => $finalTrxId,
                        'doctor_id' => $appointment->doctor_id,
                        'patient_id' => $appointment->patient_id,
                        'amount' => $fee,
                        'admin_commission' => $adminCommission,
                        'doctor_earning' => $doctorEarning,
                        'payment_method' => 'nagad',
                        'status' => 'completed',
                    ]
                );
            });

            try {
                app(SmsNotificationService::class)->sendBookingConfirmation($appointment);
            } catch (\Exception $e) {
                Log::error('[Nagad Webhook SMS Dispatch Error]: '.$e->getMessage());
            }

            Log::info("[Nagad Webhook Success]: Appointment #{$orderId} confirmed with TrxID: {$finalTrxId}");

            return response()->json(['success' => true, 'message' => 'Nagad payment confirmed.'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Nagad payment not successful.'], 200);
    }

    /**
     * Handle SSLCommerz Automated IPN Notification.
     * URL: POST /api/payments/webhook/sslcommerz
     */
    public function handleSslcommerzIpn(Request $request): JsonResponse
    {
        if (! $this->validateWebhookSecurity($request, 'sslcommerz')) {
            Log::warning('[SSLCommerz IPN Security Warning]: Rejected request due to invalid secret/signature from IP: '.$request->ip());

            return response()->json(['success' => false, 'message' => 'Unauthorized webhook invocation.'], 403);
        }

        $payload = $request->all();
        $tranId = $payload['tran_id'] ?? null;
        $valId = $payload['val_id'] ?? null;
        Log::info('[SSLCommerz IPN Notification Received]: Tran ID: '.($tranId ?: 'N/A').', Val ID: '.($valId ?: 'N/A'));
        $status = strtoupper($payload['status'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);
        $cardType = $payload['card_type'] ?? 'sslcommerz';

        if (! $tranId) {
            return response()->json(['success' => false, 'message' => 'Transaction ID missing.'], 400);
        }

        $appointment = Appointment::with(['doctor', 'chamber'])
            ->where('appointment_number', trim($tranId))
            ->first();

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        if ($appointment->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Already paid.'], 200);
        }

        if ($status === 'VALID' || $status === 'VALIDATED') {
            $currency = strtoupper($payload['currency'] ?? $payload['currency_type'] ?? 'BDT');
            if ($currency !== 'BDT') {
                Log::error("[SSLCommerz IPN Fraud Alert]: Currency mismatch for #{$tranId}. Expected: BDT, Received: {$currency}");

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid currency. Only BDT is accepted.',
                ], 422);
            }

            $requiredFee = (float) $appointment->consultation_fee;

            // Strict Amount Verification (Anti-Fraud / Underpayment check)
            if ($amount > 0 && $amount < $requiredFee) {
                Log::error("[SSLCommerz IPN Fraud Alert]: Underpayment detected for #{$tranId}. Required: {$requiredFee}, Received: {$amount}");

                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount is less than the required consultation fee.',
                ], 422);
            }

            // Server-to-server validation with SSLCommerz Gateway API if credentials configured
            $storeId = config('services.sslcommerz.store_id');
            $storePasswd = config('services.sslcommerz.store_passwd');
            if ($valId && $storeId && $storePasswd) {
                $isSandbox = config('services.sslcommerz.sandbox', true);
                $validationUrl = $isSandbox
                    ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
                    : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

                try {
                    $res = Http::timeout(10)->get($validationUrl, [
                        'val_id' => $valId,
                        'store_id' => $storeId,
                        'store_passwd' => $storePasswd,
                        'format' => 'json',
                    ]);

                    if ($res->successful()) {
                        $valData = $res->json();
                        $remoteStatus = strtoupper($valData['status'] ?? '');
                        if ($remoteStatus !== 'VALID' && $remoteStatus !== 'VALIDATED') {
                            Log::error("[SSLCommerz Server Validation Failed]: Gateway returned status {$remoteStatus}");

                            return response()->json([
                                'success' => false,
                                'message' => 'SSLCommerz payment validation rejected by gateway server.',
                            ], 422);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('[SSLCommerz Validation Request Exception]: '.$e->getMessage());
                }
            }

            $fee = $amount > 0 ? $amount : $requiredFee;
            $adminCommission = round($fee * 0.10, 2);
            $doctorEarning = round($fee - $adminCommission, 2);
            $finalTrxId = $valId ?: $tranId;

            // Replay Attack Prevention
            $existingTrx = Payment::where('transaction_id', $finalTrxId)->first();
            if ($existingTrx && $existingTrx->appointment_id !== $appointment->id) {
                Log::error("[SSLCommerz IPN Security Alert]: Replay attack detected. TrxID {$finalTrxId} already used for appointment #{$existingTrx->appointment_id}");

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID has already been utilized.',
                ], 409);
            }

            // Atomic Multi-Step Database Write
            DB::transaction(function () use ($appointment, $finalTrxId, $cardType, $fee, $adminCommission, $doctorEarning) {
                $appointment->payment_status = 'paid';
                $appointment->payment_method = 'sslcommerz_'.strtolower(explode('-', $cardType)[0]);
                $appointment->status = 'confirmed';
                $appointment->save();

                Payment::updateOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'transaction_id' => $finalTrxId,
                        'doctor_id' => $appointment->doctor_id,
                        'patient_id' => $appointment->patient_id,
                        'amount' => $fee,
                        'admin_commission' => $adminCommission,
                        'doctor_earning' => $doctorEarning,
                        'payment_method' => 'sslcommerz',
                        'status' => 'completed',
                    ]
                );
            });

            try {
                app(SmsNotificationService::class)->sendBookingConfirmation($appointment);
            } catch (\Exception $e) {
                Log::error('[SSLCommerz IPN SMS Dispatch Error]: '.$e->getMessage());
            }

            Log::info("[SSLCommerz IPN Success]: Appointment #{$tranId} confirmed with ValID: {$finalTrxId}");

            return response()->json(['success' => true, 'message' => 'SSLCommerz payment confirmed.'], 200);
        }

        return response()->json(['success' => false, 'message' => 'SSLCommerz payment status invalid.'], 200);
    }

    /**
     * Admin Manual TrxID Verification Endpoint.
     * Allows staff or administrators to verify a patient's offline bKash/Nagad TrxID in 1 click.
     * URL: POST /api/payments/webhook/manual-verify
     */
    public function verifyManualTrxId(Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস! লগইন আবশ্যক।'], 401);
        }

        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);
        $isAssistant = $authUser instanceof DoctorAssistant;
        $isDoctor = method_exists($authUser, 'isDoctor') ? $authUser->isDoctor() : ($authUser->role === 'doctor');

        if (! $isAdmin && ! $isAssistant && ! $isDoctor) {
            return response()->json([
                'success' => false,
                'message' => 'অননুমোদিত অ্যাক্সেস। শুধুমাত্র অ্যাডমিন বা অনুমোদিত স্টাফ এই কার্যক্রম সম্পন্ন করতে পারেন।',
            ], 403);
        }

        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'trx_id' => 'required|string|min:4|max:50',
            'payment_method' => 'nullable|string|in:bkash,nagad,rocket,bank,cash',
            'send_sms' => 'nullable|boolean',
        ]);

        $appointment = Appointment::with(['doctor', 'chamber'])->findOrFail($data['appointment_id']);

        if (! $isAdmin) {
            $allowedDoctorId = null;
            if ($isAssistant) {
                $allowedDoctorId = $authUser->doctor_id;
            } elseif ($isDoctor) {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                $allowedDoctorId = $doc?->id;
            }

            if ($allowedDoctorId && $appointment->doctor_id !== $allowedDoctorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র আপনার নির্ধারিত চেম্বারের অ্যাপয়েন্টমেন্ট ভেরিফাই করতে পারেন।',
                ], 403);
            }
        }
        $method = $data['payment_method'] ?? ($appointment->payment_method ?: 'bkash');
        $trxId = strtoupper(trim($data['trx_id']));
        $fee = (float) $appointment->consultation_fee;
        $adminCommission = round($fee * 0.10, 2);
        $doctorEarning = round($fee - $adminCommission, 2);

        $appointment->payment_status = 'paid';
        $appointment->payment_method = $method;
        $appointment->status = 'confirmed';
        $appointment->save();

        Payment::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'transaction_id' => $trxId,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'amount' => $fee,
                'admin_commission' => $adminCommission,
                'doctor_earning' => $doctorEarning,
                'payment_method' => $method,
                'status' => 'completed',
            ]
        );

        if ($request->input('send_sms', true)) {
            try {
                app(SmsNotificationService::class)->sendBookingConfirmation($appointment);
            } catch (\Exception $e) {
                Log::error('[Manual Payment Verify SMS Error]: '.$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "পেমেন্ট সফলভাবে ভেরিফাই হয়েছে! TrxID: {$trxId}। অ্যাপয়েন্টমেন্ট কনফার্ম করা হয়েছে।",
            'appointment' => $appointment->fresh(),
        ]);
    }
}
