<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentConfirmedMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\PrescriptionCreatedMail;
use App\Mail\RefundProcessedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function Symfony\Component\Clock\now;

class NotificationService
{
    protected $sms;

    public function __construct()
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');

        $AT = new AfricasTalking($username, $apiKey);
        $this->sms = $AT->sms();
    }

    public function sendAppointmentConfirmation(Appointment $appointment)
    {
        $patient = $appointment->patient;
        $doctor = $appointment->doctor;

        $message = "Hi {$patient->first_name} {$patient->last_name} your appointment with
          Dr.{$doctor->first_name} {$doctor->last_name} is confirmed for" .
            Carbon::parse($appointment->appointment_date)->format('M d,y') . "at" .
            Carbon::parse($appointment->appointment_time)->format('h:i A') . "." .
            "Consultation fee: UGX" . number_format($appointment->consultation_fee);
        //send sms
        $this->sendSMS($patient->user->phone, $message);

        //send email

        Mail::to($patient->user->email)->send(new AppointmentConfirmedMail($appointment));
        Notification::create([
            'user_id' => $patient->user_id,
            'type' => 'Appointment Confirmation',
            'title' => 'Appointment Confirmed',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'read_at' => now(),
            'metadata' => json_encode([$appointment->appointment_id])
        ]);
    }

    public function sendPrescriptionDispensed($prescription)
    {
        $patient = $prescription->patient;

        $message = "Your prescription ({$prescription->prescription_number}) has been dispensed. Please check your account for details.";

        $this->sendSMS($patient->user->phone, $message);
        Mail::to($patient->user->email)->send(new PrescriptionCreatedMail());
        Notification::create([
            'user_id' => $patient->user_id,
            'type' => 'prescription',
            'title' => 'Prescription Dispensed',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => json_encode(['prescription_id' => $prescription->id])
        ]);
    }

    public function sendPrescriptionCreated($prescription)
    {
        $patient = $prescription->patient;

        $message = "A new prescription ({$prescription->prescription_number}) has been created for you. Please check your account for details.";

        $this->sendSMS($patient->user->phone, $message);
        Mail::to($patient->user->email)->send(new PrescriptionCreatedMail());
        Notification::create([
            'user_id' => $patient->user_id,
            'type' => 'prescription',
            'title' => 'New Prescription Created',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => json_encode(['prescription_id' => $prescription->id])
        ]);
    }
    public function  sendAppointmentCancellation(Appointment $appointment)
    {
        $patient = $appointment->patient;

        $message = "Your appointment {$appointment->appointment_number} has been cancelled." . ($appointment->payment_status === 'refunded' ? "Refund has been processed." : "");

        $this->sendSMS($patient->user->phone, $message);
        Mail::to($patient->user->email)->send(new AppointmentCancelledMail($appointment));
    }

    public function sendRefundNotification(Payment $payment)
    {

        $patient = $payment->patient;

        $message = "Refund of UGX:" . number_format($payment->refund_amount) . "has been proceded to your account";
        $this->sendSMS($patient->user->phone, $message);
        Mail::to($patient->user->email)->send(new RefundProcessedMail($payment));
        Notification::create([
            'user_id' => $patient->user_id,
            'type' => 'refund',
            'title' => 'Payment Refunded',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => json_encode(['payment_id' => $payment->id])
        ]);
    }

    public function sendPaymentConfirmation(Payment $payment)
    {

        $appointment = $payment->appointment;
        $patient = $appointment->patient;

        $message = "Payment Successful! UGX" . number_format($payment->amount) . "received for appointment" .
            $appointment->appointment_number . "Thank you!";

        $this->sendSMS($patient->user->phone, $message);
        Mail::to($patient->user->email)->send(new PaymentConfirmedMail($payment));
        Notification::create([
            'user_id' => $patient->user_id,
            'type' => 'payment_confirmation',
            'title' => 'Payment Confirmed',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => json_encode(['payment_id' => $payment->id])
        ]);
    }

    private function sendSMS($phone, $message)
    {
        try {
            $result = $this->sms->send([
                'to' => $phone,
                'message' => $message
            ]);
            Log::info("SMS sent to {$phone}: " . json_encode($result));
        } catch (Exception $e) {
            Log::error("Failed to send SMS to {$phone}: " . $e->getMessage());
        }
    }
}
