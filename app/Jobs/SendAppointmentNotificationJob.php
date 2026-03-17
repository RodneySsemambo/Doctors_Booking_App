<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\NotificationService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mockery\Matcher\Not;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $appointment;
    public $tries = 3;
    public $timeout = 60;
    public $backoff = 10;
    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->onQueue('notifications');
    }


    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            $notificationService->sendAppointmentConfirmation($this->appointment);
            Log::info('Appointment notification sent successfully for Appointment ID: ' . $this->appointment->id);
        } catch (\Exception $e) {
            Log::error('Failed to send appointment notification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('SendAppointmentNotificationJob failed for Appointment ID: ' . $this->appointment->id . ' with error: ' . $exception->getMessage());
    }
}
