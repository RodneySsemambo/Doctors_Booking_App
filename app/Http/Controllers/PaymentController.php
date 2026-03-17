<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle MarzPay webhook callback
     */
    public function handleMarzPayCallback(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('MarzPay Webhook Received:', $data);

            $payment = $this->paymentService->handleMarzPayCallback($data);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook Processing Error:', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($paymentId)
    {
        try {
            $payment = $this->paymentService->verifyPayment($paymentId);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Test MarzPay connection
     */
    public function testConnection()
    {
        try {
            $result = $this->paymentService->testMarzPayConnection();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details
     */
    public function getPayment($paymentId)
    {
        try {
            $payment = \App\Models\Payment::with(['appointment.doctor', 'patient'])
                ->findOrFail($paymentId);

            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found: ' . $e->getMessage()
            ], 404);
        }
    }
}
