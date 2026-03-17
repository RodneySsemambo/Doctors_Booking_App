<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminderJob implements ShouldQueue
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
        $this->onQueue('reminders');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $patient = $this->appointment->patient;
            $doctor = $this->appointment->doctor;
            $message = "Reminder: You have an appointment with Dr. {$doctor->first_name} {$doctor->last_name} on " .
                $this->appointment->appointment_date->format('M d, Y') . " at " .
                $this->appointment->appointment_time->format('h:i A') . ".";
            Mail::to($patient->user->email)->send(new AppointmentReminderMail($this->appointment, $patient, $doctor));
            Log::info('Appointment reminder email sent to ' . $patient->user->email . ' for Appointment ID: ' . $this->appointment->id);
        } catch (Exception $e) {
            Log::error('Failed to send appointment reminder email: ' . $e->getMessage());
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('SendAppointmentReminderJob failed for Appointment ID: ' . $this->appointment->id . ' with error: ' . $exception->getMessage());
    }
}
