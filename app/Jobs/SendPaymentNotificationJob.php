<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\NotificationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentNotificationJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $payment;
    public $tries = 3;
    public $timeout = 60;
    public $backoff = 10;
    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            $notificationService->sendPaymentConfirmation($this->payment);
            Log::info('Payment notification sent successfully for Payment ID: ' . $this->payment->id);
        } catch (Exception $e) {
            Log::error('Failed to send payment notification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('SendPaymentNotificationJob failed for Payment ID: ' . $this->payment->id . ' with error: ' . $exception->getMessage());
    }
}
