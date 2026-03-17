<?php

namespace App\Livewire\Patient\Appointments;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\PaymentService;
use App\Jobs\SendAppointmentNotificationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BookAppointment extends Component
{
    // Step management
    public int $currentStep = 1;
    public int $totalSteps  = 4;

    // Step 1
    public $specializations        = [];
    public $hospitals              = [];
    public $doctors                = [];
    public $selectedSpecialization = null;
    public $selectedHospital       = null;
    public $selectedDoctor         = null;
    public $doctorDetails          = null;

    // Step 2
    public $selectedDate       = null;
    public $availableTimeSlots = [];
    public $selectedTime       = null;
    public $appointmentType    = 'in-person';

    // Step 3
    public string $reason   = '';
    public string $symptoms = '';
    public string $notes    = '';
    public $patient         = null;

    // Step 4
    public string $paymentMethod   = 'mtn_mobile_money';
    public string $phoneNumber     = '';
    public float  $consultationFee = 0;
    public bool   $agreedToTerms   = false;

    // ------------------------------------------------------------------ mount
    public function mount(): void
    {
        $this->specializations = Specialization::where('is_active', true)->get();
        $this->hospitals       = Hospital::where('is_active', true)->get();
        $this->loadDoctors();

        if (Auth::check() && Auth::user()->patient) {
            $this->patient     = Auth::user()->patient;
            $this->phoneNumber = Auth::user()->phone ?? '';
        }

        $this->selectedDate = Carbon::tomorrow()->format('Y-m-d');
    }

    // ------------------------------------------------------------ watchers
    public function updatedSelectedSpecialization(): void
    {
        $this->loadDoctors();
        $this->selectedDoctor = null;
        $this->doctorDetails  = null;
    }

    public function updatedSelectedHospital(): void
    {
        $this->loadDoctors();
        $this->selectedDoctor = null;
        $this->doctorDetails  = null;
    }

    public function updatedSelectedDoctor($value): void
    {
        if ($value) {
            $this->doctorDetails   = Doctor::with(['specialization', 'hospital'])->find($value);
            $this->consultationFee = (float) ($this->doctorDetails->consultation_fee ?? 0);
            $this->loadAvailableTimeSlots();
        } else {
            $this->doctorDetails   = null;
            $this->consultationFee = 0;
        }
    }

    public function updatedSelectedDate(): void
    {
        if ($this->selectedDoctor && $this->selectedDate) {
            $this->loadAvailableTimeSlots();
        }
    }

    // -------------------------------------------------------- data loaders
    protected function loadDoctors(): void
    {
        $query = Doctor::query()
            ->where('is_verified', true)
            ->with(['specialization', 'hospital']);

        if (!empty($this->selectedSpecialization)) {
            $query->where('specialization_id', $this->selectedSpecialization);
        }

        $this->doctors = $query->get();
    }

    protected function loadAvailableTimeSlots(): void
    {
        $this->availableTimeSlots = [];
        $this->selectedTime       = null;

        if (!$this->selectedDoctor || !$this->selectedDate) {
            return;
        }

        // ✅ Use AppointmentService::getAvailableSlots() so slot loading and
        //    booking use exactly the same logic — no drift between the two.
        $slots = app(AppointmentService::class)
            ->getAvailableSlots($this->selectedDoctor, $this->selectedDate);

        $this->availableTimeSlots = collect($slots)->map(fn($time) => [
            'value' => substr($time, 0, 5),                           // 'HH:MM'
            'label' => Carbon::parse($time)->format('h:i A'),         // '09:00 AM'
        ])->values()->toArray();
    }

    // --------------------------------------------------- step navigation
    public function nextStep(): void
    {
        $this->validate(
            $this->rulesForStep($this->currentStep),
            $this->messagesForStep($this->currentStep)
        );

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->resetErrorBag();
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->resetErrorBag();
        }
    }

    // ------------------------------------------------- validation rules
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'selectedDoctor' => 'required|exists:doctors,id',
            ],
            2 => [
                'selectedDate'    => 'required|date|after:today',
                'selectedTime'    => count($this->availableTimeSlots) > 0
                    ? 'required'
                    : 'nullable',
                'appointmentType' => 'required|in:in-person,virtual',
            ],
            3 => [
                'reason' => 'required|string|min:10|max:500',
            ],
            4 => [
                'paymentMethod' => 'required|in:mtn_mobile_money,airtel_mobile_money,cash',
                'phoneNumber'   => 'required_unless:paymentMethod,cash|string',
                'agreedToTerms' => 'accepted',
            ],
            default => [],
        };
    }

    protected function messagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'selectedDoctor.required' => 'Please select a doctor to continue.',
                'selectedDoctor.exists'   => 'The selected doctor is invalid.',
            ],
            2 => [
                'selectedDate.required'    => 'Please select a date.',
                'selectedDate.after'       => 'The appointment date must be in the future.',
                'selectedTime.required'    => 'Please select a time slot.',
                'appointmentType.required' => 'Please select an appointment type.',
            ],
            3 => [
                'reason.required' => 'Please provide a reason for your visit.',
                'reason.min'      => 'Please give a bit more detail (at least 10 characters).',
            ],
            4 => [
                'paymentMethod.required'      => 'Please select a payment method.',
                'phoneNumber.required_unless' => 'Please enter your phone number for mobile money.',
                'agreedToTerms.accepted'      => 'You must agree to the terms to continue.',
            ],
            default => [],
        };
    }

    // ---------------------------------------------------- book appointment
    public function bookAppointment(): void
    {
        $this->validate(
            $this->rulesForStep(4),
            $this->messagesForStep(4)
        );

        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if (!$this->patient) {
            session()->flash('error', 'Patient profile not found. Please complete your profile first.');
            return;
        }

        try {
            // ✅ FIX: route through AppointmentService::createAppointment()
            //    so checkSlotAvailability() runs and concurrent double-bookings
            //    are prevented. Previously Appointment::create() was called
            //    directly, completely bypassing the slot conflict check.
            $appointment = app(AppointmentService::class)->createAppointment([
                'patient_id'       => $this->patient->id,
                'doctor_id'        => $this->selectedDoctor,
                'hospital_id'      => $this->selectedHospital ?? $this->doctorDetails?->hospitals?->first()?->id,
                'appointment_date' => $this->selectedDate,
                // Service stores 'H:i:s'; selectedTime is 'H:i' — pad it
                'appointment_time' => $this->selectedTime
                    ? Carbon::parse($this->selectedTime)->format('H:i:s')
                    : null,
                'appointment_type' => $this->appointmentType,
                'reason_for_visit' => $this->reason,
                'symptoms'         => $this->symptoms ?: $this->reason,
                'notes'            => $this->notes ?: null,
            ]);

            // Payment
            if ($this->paymentMethod !== 'cash') {
                try {
                    $paymentResult = app(PaymentService::class)->initiatePayment([
                        'appointment_id' => $appointment->id,
                        'patient_id'     => $this->patient->id,
                        'amount'         => $this->consultationFee,
                        'currency'       => 'UGX',
                        'phone_number'   => $this->phoneNumber,
                        'payment_method' => $this->paymentMethod,
                    ]);

                    if ($paymentResult['success'] ?? false) {
                        SendAppointmentNotificationJob::dispatch($appointment);
                        session()->flash('success', 'Appointment booked! Check your phone to complete payment.');
                    } else {
                        $appointment->update(['payment_status' => 'pending']);
                        session()->flash('success', 'Appointment booked! You can complete payment later.');
                    }
                } catch (\Exception $e) {
                    Log::error('Payment error: ' . $e->getMessage());
                    $appointment->update(['payment_status' => 'pending']);
                    session()->flash('success', 'Appointment booked! Pay at the hospital.');
                }
            } else {
                SendAppointmentNotificationJob::dispatch($appointment);
                session()->flash('success', 'Appointment booked! Pay cash at the hospital.');
            }

            $this->redirect(route('patient.appointments'));
        } catch (\Exception $e) {
            // ✅ If the slot was taken between step 2 and step 4 (race condition),
            //    the service throws 'The time slot is no longer available' and
            //    we show it as a user-friendly error instead of a 500.
            Log::error('Booking error: ' . $e->getMessage(), [
                'patient_id'     => $this->patient?->id,
                'doctor_id'      => $this->selectedDoctor,
                'date'           => $this->selectedDate,
                'time'           => $this->selectedTime,
                'payment_method' => $this->paymentMethod,
            ]);

            $userMessage = str_contains($e->getMessage(), 'time slot')
                ? 'That time slot was just taken. Please go back and choose another.'
                : 'Booking failed: ' . $e->getMessage();

            session()->flash('error', $userMessage);
        }
    }

    public function render()
    {
        return view('livewire.patient.appointments.book-appointment')
            ->layout('layouts.patient');
    }
}
