<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected $marzpayApiKey;
    protected $marzpayApiSecret;
    protected $marzpayBaseUrl;

    public function __construct()
    {
        $this->marzpayApiKey = config('services.marzpay.api_key');
        $this->marzpayApiSecret = config('services.marzpay.api_secret');
        $this->marzpayBaseUrl = config('services.marzpay.base_url', 'https://wallet.wearemarz.com/api/v1');
    }

    /**
     * Generate Basic Auth credentials for MarzPay
     */
    protected function getMarzpayCredentials()
    {
        $credentials = $this->marzpayApiKey . ':' . $this->marzpayApiSecret;
        return 'Basic ' . base64_encode($credentials);
    }

    /**
     * Initiate payment
     */
    public function initiatePayment($data)
    {
        DB::beginTransaction();
        try {
            // Get appointment
            $appointment = Appointment::findOrFail($data['appointment_id']);

            // Generate payment number
            $paymentNumber = $this->getPaymentNumber();

            // Create payment record
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'appointment_id' => $data['appointment_id'],
                'patient_id' => $data['patient_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'UGX',
                'phone_number' => $data['phone_number'],
                'payment_method' => $data['payment_method'],
                'payment_provider' => $this->getProviderFromMethod($data['payment_method']),
                'status' => 'pending',
                'transaction_reference' => Str::uuid(),
                'initiated_at' => now(),
            ]);

            // Process payment based on method
            if (in_array($data['payment_method'], ['mtn_mobile_money', 'airtel_mobile_money'])) {
                $result = $this->processMarzPayMobileMoneyPayment($payment);
            } elseif ($data['payment_method'] === 'flutterwave') {
                $result = $this->processFlutterwavePayment($payment);
            } elseif ($data['payment_method'] === 'cash') {
                $result = $this->processCashPayment($payment);
            } else {
                throw new Exception('Invalid payment method');
            }

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment->fresh(),
                'provider_data' => $result
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment initiation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Process mobile money payment via MarzPay
     */
    protected function processMarzPayMobileMoneyPayment(Payment $payment)
    {
        try {
            Log::info('Processing MarzPay mobile money payment', [
                'payment_id' => $payment->id,
                'reference' => $payment->transaction_reference,
                'amount' => $payment->amount,
                'phone' => $payment->phone_number,
            ]);

            // Prepare request data according to MarzPay API docs
            $requestData = [
                'amount' => (int) $payment->amount,
                'phone_number' => $payment->phone_number,
                'country' => 'UG',
                'reference' => $payment->transaction_reference,
                'description' => "Payment for appointment #{$payment->appointment_id}",
                'callback_url' => route('api.payments.marzpay.callback'),
            ];

            Log::info('MarzPay API Request Data:', $requestData);

            // Make API request to MarzPay
            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->marzpayBaseUrl . '/collect-money', $requestData);

            Log::info('MarzPay API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success') {
                    $transactionUuid = $data['data']['transaction']['uuid'] ?? null;
                    $providerReference = $data['data']['transaction']['provider_reference'] ?? null;

                    $payment->update([
                        'provider_reference' => $transactionUuid,
                        'provider_response' => $data,
                        'status' => 'processing',
                    ]);

                    Log::info('MarzPay payment initiated successfully', [
                        'transaction_uuid' => $transactionUuid,
                        'payment_id' => $payment->id,
                    ]);

                    return $data;
                } else {
                    throw new Exception('MarzPay API returned error: ' . ($data['message'] ?? 'Unknown error'));
                }
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? $response->body();
                throw new Exception('MarzPay request failed: ' . $errorMessage);
            }
        } catch (Exception $e) {
            Log::error('MarzPay mobile money payment failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $payment->update([
                'status' => 'failed',
                'failed_reason' => $e->getMessage()
            ]);

            throw $e;
        }
    }


    /**
     * Verify MarzPay payment status
     */
    public function verifyPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        try {
            // Get provider response - handle both array and JSON string
            $providerResponse = $payment->provider_response;
            if (is_string($providerResponse)) {
                $providerResponse = json_decode($providerResponse, true);
            }

            // Check if it's sandbox mode
            if (
                isset($providerResponse['data']['metadata']['sandbox_mode']) &&
                $providerResponse['data']['metadata']['sandbox_mode'] === true
            ) {

                // For sandbox payments, simulate completion
                Log::info('Processing sandbox payment verification', [
                    'payment_id' => $payment->id,
                    'sandbox' => true,
                ]);

                // Get sandbox provider reference from response
                $sandboxReference = $providerResponse['data']['transaction']['provider_reference'] ?? null;

                // updated provider response
                $updatedProviderResponse = $providerResponse;
                $updatedProviderResponse['verified_at'] = now()->toDateTimeString();
                $updatedProviderResponse['sandbox_verified'] = true;

                // Updating the payment with proper JSON encoding
                $payment->update([
                    'provider_reference' => $sandboxReference,
                    'status' => 'completed',
                    'completed_at' => now(),
                    'provider_response' => json_encode($updatedProviderResponse),
                ]);

                // Update appointment payment status
                if ($payment->appointment) {
                    $payment->appointment->update(['payment_status' => 'paid']);
                }

                Log::info('Sandbox payment verified successfully', [
                    'payment_id' => $payment->id,
                ]);

                return $payment->fresh();
            }

            // For real payments, check provider_reference
            if (!$payment->provider_reference) {
                // Try to extract from provider_response if not saved
                if (isset($providerResponse['data']['transaction']['uuid'])) {
                    $payment->update([
                        'provider_reference' => $providerResponse['data']['transaction']['uuid']
                    ]);
                    $payment->refresh();
                } else {
                    throw new Exception('No transaction reference available for verification');
                }
            }

            Log::info('Verifying MarzPay payment', [
                'payment_id' => $payment->id,
                'transaction_uuid' => $payment->provider_reference,
            ]);

            // Check collection status
            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Accept' => 'application/json',
            ])->timeout(15)->get($this->marzpayBaseUrl . '/collect-money/' . $payment->provider_reference);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success') {
                    $transactionStatus = $data['data']['transaction']['status'] ?? null;

                    Log::info('MarzPay verification response', [
                        'transaction_status' => $transactionStatus,
                        'data' => $data,
                    ]);

                    // Prepare updated provider response
                    $currentResponse = $payment->provider_response;
                    if (is_string($currentResponse)) {
                        $currentResponse = json_decode($currentResponse, true);
                    }

                    $updatedResponse = $currentResponse ?? [];
                    $updatedResponse['verification_data'] = $data;
                    $updatedResponse['verified_at'] = now()->toDateTimeString();

                    if ($transactionStatus === 'successful' || $transactionStatus === 'sandbox') {
                        $payment->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'provider_response' => json_encode($updatedResponse), // Encode to JSON
                        ]);

                        // Update appointment payment status
                        if ($payment->appointment) {
                            $payment->appointment->update(['payment_status' => 'paid']);
                        }

                        Log::info('Payment verified and completed', [
                            'payment_id' => $payment->id,
                            'appointment_id' => $payment->appointment_id,
                        ]);
                    } elseif ($transactionStatus === 'failed') {
                        $payment->update([
                            'status' => 'failed',
                            'failed_reason' => 'Transaction failed at MarzPay',
                            'provider_response' => json_encode($updatedResponse), // Encode to JSON
                        ]);
                    }

                    return $payment->fresh();
                }
            }

            // If specific endpoint fails, try transactions endpoint as fallback
            return $this->verifyViaTransactionsEndpoint($payment);
        } catch (Exception $e) {
            Log::error('MarzPay verification failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment via transactions endpoint (fallback)
     */
    /**
     * Verify payment via transactions endpoint (fallback)
     */
    protected function verifyViaTransactionsEndpoint(Payment $payment)
    {
        try {
            // Get transactions list filtered by reference
            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Accept' => 'application/json',
            ])->timeout(15)->get($this->marzpayBaseUrl . '/transactions', [
                'reference' => $payment->transaction_reference,
                'per_page' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (
                    isset($data['status']) && $data['status'] === 'success' &&
                    !empty($data['data']['transactions'])
                ) {

                    $transaction = $data['data']['transactions'][0];

                    // Prepare updated provider response
                    $currentResponse = $payment->provider_response;
                    if (is_string($currentResponse)) {
                        $currentResponse = json_decode($currentResponse, true);
                    }

                    $updatedResponse = $currentResponse ?? [];
                    $updatedResponse['verification_data'] = $data;
                    $updatedResponse['verified_at'] = now()->toDateTimeString();

                    if ($transaction['status'] === 'successful') {
                        $payment->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'provider_response' => json_encode($updatedResponse), // Encode to JSON
                        ]);

                        $payment->appointment->update(['payment_status' => 'paid']);
                    }

                    return $payment->fresh();
                }
            }

            throw new Exception('Could not verify payment status');
        } catch (Exception $e) {
            Log::warning('Transactions endpoint verification failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle MarzPay webhook callback
     */
    public function handleMarzPayCallback(array $data)
    {
        DB::beginTransaction();

        try {
            Log::info('MarzPay webhook received:', $data);

            // Extract transaction reference from webhook data
            $reference = $data['reference'] ?? $data['data']['transaction']['reference'] ?? null;

            if (!$reference) {
                throw new Exception('No reference found in webhook data');
            }

            // Find payment by reference
            $payment = Payment::where('transaction_reference', $reference)
                ->orWhere('provider_reference', $reference)
                ->firstOrFail();

            Log::info('Processing webhook for payment:', [
                'payment_id' => $payment->id,
                'reference' => $reference,
                'current_status' => $payment->status,
            ]);

            // Determine status from webhook
            $webhookStatus = $data['status'] ?? $data['data']['transaction']['status'] ?? null;
            $transactionStatus = $data['data']['transaction']['status'] ?? $webhookStatus;

            if ($transactionStatus === 'successful') {
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'provider_response' => array_merge(
                        $payment->provider_response ?? [],
                        ['webhook_data' => $data, 'webhook_received_at' => now()]
                    ),
                ]);

                $payment->appointment->update(['payment_status' => 'paid']);

                Log::info('Payment completed via webhook', [
                    'payment_id' => $payment->id,
                    'appointment_id' => $payment->appointment_id,
                ]);
            } elseif (in_array($transactionStatus, ['failed', 'cancelled'])) {
                $payment->update([
                    'status' => 'failed',
                    'failed_reason' => 'Payment ' . $transactionStatus . ' via webhook',
                    'provider_response' => array_merge(
                        $payment->provider_response ?? [],
                        ['webhook_data' => $data]
                    ),
                ]);
            }

            DB::commit();

            return $payment->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'webhook_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get MarzPay account balance
     */
    public function getMarzPayBalance()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Accept' => 'application/json',
            ])->timeout(10)->get($this->marzpayBaseUrl . '/balance');

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get balance: ' . $response->body());
        } catch (Exception $e) {
            Log::error('MarzPay balance check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get available MarzPay services
     */
    public function getMarzPayServices($type = 'collection', $provider = null)
    {
        try {
            $queryParams = ['type' => $type];
            if ($provider) {
                $queryParams['provider'] = $provider;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Accept' => 'application/json',
            ])->timeout(10)->get($this->marzpayBaseUrl . '/services', $queryParams);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get services: ' . $response->body());
        } catch (Exception $e) {
            Log::error('MarzPay services check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get MarzPay transactions
     */
    public function getMarzPayTransactions($filters = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getMarzpayCredentials(),
                'Accept' => 'application/json',
            ])->timeout(15)->get($this->marzpayBaseUrl . '/transactions', $filters);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get transactions: ' . $response->body());
        } catch (Exception $e) {
            Log::error('MarzPay transactions check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if MarzPay API is working
     */
    public function testMarzPayConnection()
    {
        try {
            $balance = $this->getMarzPayBalance();
            $services = $this->getMarzPayServices('collection', 'mtn');

            return [
                'success' => true,
                'balance' => $balance,
                'services' => $services,
                'message' => 'MarzPay API connection successful',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'MarzPay API connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process Flutterwave payment (for reference)
     */

    /**
     * Process cash payment
     */
    protected function processCashPayment(Payment $payment)
    {
        $payment->update([
            'status' => 'pending',
            'provider_response' => ['method' => 'cash'],
        ]);

        return ['message' => 'Cash payment pending confirmation'];
    }

    /**
     * Generate unique payment number
     */
    protected function getPaymentNumber()
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }

    /**
     * Get provider from payment method
     */
    protected function getProviderFromMethod($method)
    {
        return [
            'mtn_mobile_money' => 'mtn',
            'airtel_mobile_money' => 'airtel',
            'flutterwave' => 'flutterwave',
            'cash' => 'cash',
        ][$method] ?? 'cash';
    }

    /**
     * Get payment history for patient
     */
    public function getPatientPayments($patientId, $filters = [])
    {
        $query = Payment::where('patient_id', $patientId)
            ->with(['appointment.doctor']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 20);
    }
}
