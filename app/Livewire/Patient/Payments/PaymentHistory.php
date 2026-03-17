<?php

namespace App\Livewire\Patient\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentHistory extends Component
{
    use WithPagination;

    // State properties
    public $searchTerm = '';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $paymentMethodFilter = 'all';

    // Modal properties
    public $showDetailsModal = false;
    public $showReceiptModal = false;
    public $selectedPayment = null;

    // Payment initiation properties
    public $showPaymentModal = false;
    public $paymentAppointmentId = null;
    public $paymentAmount = 0;
    public $paymentPhone = '';
    public $paymentMethod = 'mtn_mobile_money';

    // Statistics
    public $stats = [
        'total' => 0,
        'completed' => 0,
        'pending' => 0,
        'failed' => 0,
    ];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'paymentMethodFilter' => ['except' => 'all'],
    ];

    protected $paymentService;
    public $paymentMethods = [
        'mtn_mobile_money' => 'MTN Mobile Money',
        'airtel_mobile_money' => 'Airtel Money',
        'flutterwave' => 'Card/Flutterwave',
        'cash' => 'Cash',
    ];

    public function boot()
    {
        $this->paymentService = app(PaymentService::class);
    }

    public function mount()
    {
        $this->loadStatistics();
        if (Auth::check() && Auth::user()->phone) {
            $this->paymentPhone = Auth::user()->phone;
        }
    }

    public function loadStatistics()
    {
        if (Auth::check() && Auth::user()->patient) {
            $patientId = Auth::user()->patient->id;

            $this->stats['total'] = Payment::where('patient_id', $patientId)->count();
            $this->stats['completed'] = Payment::where('patient_id', $patientId)
                ->where('status', 'completed')->count();
            $this->stats['pending'] = Payment::where('patient_id', $patientId)
                ->whereIn('status', ['pending', 'processing'])->count();
            $this->stats['failed'] = Payment::where('patient_id', $patientId)
                ->where('status', 'failed')->count();
        }
    }

    public function getPaymentsProperty()
    {
        if (!Auth::check() || !Auth::user()->patient) {
            return collect();
        }

        $query = Payment::where('patient_id', Auth::user()->patient->id)
            ->with(['appointment.doctor'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->paymentMethodFilter !== 'all') {
            $query->where('payment_method', $this->paymentMethodFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->searchTerm) {
            $search = strtolower($this->searchTerm);
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('appointment', function ($query) use ($search) {
                        $query->whereHas('doctor', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    });
            });
        }

        return $query->paginate(10);
    }

    // NEW: Open payment modal without parameters
    public function openPaymentModal()
    {
        $this->showPaymentModal = true;
    }

    public function viewDetails($paymentId)
    {
        try {
            $this->selectedPayment = Payment::with(['appointment.doctor', 'patient'])
                ->findOrFail($paymentId);

            // Verify ownership
            if ($this->selectedPayment->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to view this payment.');
                return;
            }

            $this->showDetailsModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Payment not found: ' . $e->getMessage());
        }
    }

    public function viewReceipt($paymentId)
    {
        try {
            $this->selectedPayment = Payment::with(['appointment.doctor', 'patient'])
                ->findOrFail($paymentId);

            // Verify ownership
            if ($this->selectedPayment->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to view this receipt.');
                return;
            }

            $this->showReceiptModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Payment not found: ' . $e->getMessage());
        }
    }

    // Overloaded method for opening modal with pre-filled data
    public function openPaymentModalWithData($appointmentId = null, $amount = null)
    {
        $this->paymentAppointmentId = $appointmentId;
        $this->paymentAmount = $amount ?? 0;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset([
            'paymentAppointmentId',
            'paymentAmount',
            'paymentPhone',
            'paymentMethod'
        ]);
        $this->resetErrorBag();
    }

    public function initiatePayment()
    {
        $this->validate([
            'paymentAppointmentId' => 'required|exists:appointments,id',
            'paymentAmount' => 'required|numeric|min:500|max:10000000',
            'paymentPhone' => 'required|string|regex:/^\+256[0-9]{9}$/',
            'paymentMethod' => 'required|in:mtn_mobile_money,airtel_mobile_money,flutterwave,cash',
        ]);

        try {
            if (!Auth::user()->patient) {
                throw new \Exception('Patient profile not found');
            }

            // Verify appointment belongs to patient
            $appointment = \App\Models\Appointment::find($this->paymentAppointmentId);
            if ($appointment->patient_id !== Auth::user()->patient->id) {
                throw new \Exception('This appointment does not belong to you');
            }

            $data = [
                'appointment_id' => $this->paymentAppointmentId,
                'patient_id' => Auth::user()->patient->id,
                'amount' => $this->paymentAmount,
                'currency' => 'UGX',
                'phone_number' => $this->paymentPhone,
                'payment_method' => $this->paymentMethod,
            ];

            $result = $this->paymentService->initiatePayment($data);

            $this->closePaymentModal();
            $this->loadStatistics();

            // Show success message
            if ($this->paymentMethod === 'cash') {
                session()->flash('success', 'Cash payment recorded. Please pay at the hospital reception.');
            } else {
                session()->flash('success', 'Payment initiated successfully! Please check your phone to complete the payment.');

                // You might want to show the transaction reference
                session()->flash('info', 'Transaction Reference: ' . $result['payment']['transaction_reference']);
            }

            // If there's a redirect URL (for Flutterwave), redirect user
            if (isset($result['provider_data']['link'])) {
                return redirect()->away($result['provider_data']['link']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Payment initiation failed: ' . $e->getMessage());

            // Log detailed error
            Log::error('Payment initiation error: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => [
                    'appointment_id' => $this->paymentAppointmentId,
                    'amount' => $this->paymentAmount,
                    'phone' => $this->paymentPhone,
                    'method' => $this->paymentMethod,
                ]
            ]);
        }
    }

    //  Verify payment status
    public function verifyPayment($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            // Verify ownership
            if ($payment->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to verify this payment.');
                return;
            }

            $this->paymentService->verifyPayment($paymentId);
            $this->loadStatistics();

            session()->flash('success', 'Payment status verified successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Verification failed: ' . $e->getMessage());
        }
    }

    //  Retry failed payment
    public function retryPayment($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            // Verify ownership
            if ($payment->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to retry this payment.');
                return;
            }

            if ($payment->status !== 'failed') {
                session()->flash('error', 'Only failed payments can be retried.');
                return;
            }

            $this->paymentAppointmentId = $payment->appointment_id;
            $this->paymentAmount = $payment->amount;
            $this->paymentPhone = $payment->phone_number;
            $this->paymentMethod = $payment->payment_method;

            $this->showPaymentModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load payment: ' . $e->getMessage());
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPayment = null;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->selectedPayment = null;
    }

    // NEW: Download receipt
    public function downloadReceipt($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            // Verify ownership
            if ($payment->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to download this receipt.');
                return;
            }

            if ($payment->status !== 'completed') {
                session()->flash('error', 'Receipt is only available for completed payments.');
                return;
            }

            // Generate PDF receipt using your PDF service
            if (class_exists('App\Services\PdfService')) {
                $pdfService = app('App\Services\PdfService');
                $pdf = $pdfService->generatePaymentReceipt($payment);

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf;
                }, "receipt-{$payment->payment_number}.pdf", [
                    'Content-Type' => 'application/pdf',
                ]);
            } else {
                // Fallback: Show receipt in modal
                $this->viewReceipt($paymentId);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download receipt: ' . $e->getMessage());
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.patient.payments.payment-history', [
            'payments' => $this->payments,
        ])
            ->layout('layouts.patient');
    }
}
