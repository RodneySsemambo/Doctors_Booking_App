<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;


class Withdrawals extends Component
{
    use WithPagination;

    public $doctor;

    // Withdrawal Request
    public $showRequestModal = false;
    public $requestAmount = 0;
    public $withdrawalMethod = 'mobile_money';
    public $methodDetails = [];

    // Available balance
    public $availableBalance = 0;
    public $pendingWithdrawals = 0;
    public $totalEarnings = 0;
    public $withdrawalFeeRate = 50; // 2.5% withdrawal fee

    // Bank Details (for bank transfer)
    public $bankName = '';
    public $accountNumber = '';
    public $accountName = '';
    public $branchCode = '';

    // Mobile Money Details
    public $mobileProvider = 'mtn';
    public $mobileNumber = '';
    public $mobileAccountName = '';

    // PayPal Details
    public $paypalEmail = '';
    public $paypalAccountName = '';

    // Filter
    public $statusFilter = 'all';
    public $perPage = 10;

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
        $this->loadStatistics();
        $this->loadSavedDetails();
    }

    public function loadStatistics()
    {
        // Calculate available balance (completed payments not yet withdrawn, with hold period)
        $this->availableBalance = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->where('completed_at', '<=', now()->subDays(3))
            ->whereDoesntHave('withdrawals')
            ->sum('amount');

        // Pending withdrawals
        $this->pendingWithdrawals = Withdrawal::where('doctor_id', $this->doctor->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        // Total earnings (all completed payments)
        $this->totalEarnings = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function loadSavedDetails()
    {
        // Load saved withdrawal details from doctor settings
        $this->bankName = $this->doctor->bank_name ?? '';
        $this->accountNumber = $this->doctor->account_number ?? '';
        $this->accountName = $this->doctor->account_name ?? '';
        $this->branchCode = $this->doctor->branch_code ?? '';
        $this->mobileNumber = $this->doctor->mobile_money_number ?? '';
        $this->mobileAccountName = $this->doctor->mobile_money_name ?? '';
        $this->paypalEmail = $this->doctor->paypal_email ?? '';
        $this->paypalAccountName = $this->doctor->paypal_account_name ?? '';

        // Default method details
        $this->methodDetails = [
            'bank_name' => $this->bankName,
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName,
            'branch_code' => $this->branchCode,
        ];
    }

    public function updatedWithdrawalMethod($method)
    {
        switch ($method) {
            case 'bank_transfer':
                $this->methodDetails = [
                    'bank_name' => $this->bankName,
                    'account_number' => $this->accountNumber,
                    'account_name' => $this->accountName,
                    'branch_code' => $this->branchCode,
                ];
                break;
            case 'mobile_money':
                $this->methodDetails = [
                    'provider' => $this->mobileProvider,
                    'phone_number' => $this->mobileNumber,
                    'account_name' => $this->mobileAccountName,
                ];
                break;
            case 'paypal':
                $this->methodDetails = [
                    'email' => $this->paypalEmail,
                    'account_name' => $this->paypalAccountName,
                ];
                break;
            case 'cash':
                $this->methodDetails = [
                    'pickup_location' => 'Clinic Office',
                    'contact_person' => 'Front Desk',
                ];
                break;
        }
    }

    public function openRequestModal()
    {
        if ($this->availableBalance <= 0) {
            session()->flash('error', 'No funds available for withdrawal.');
            return;
        }

        $this->requestAmount = $this->availableBalance;
        $this->showRequestModal = true;
    }

    public function requestWithdrawal()
    {
        $this->validate([
            'requestAmount' => [
                'required',
                'numeric',
                'min:20000', // Minimum 20,000 UGX
                'max:' . $this->availableBalance
            ],
            'withdrawalMethod' => 'required|in:bank_transfer,mobile_money,cash',
            'methodDetails' => 'required|array',
        ]);

        // Validate method details based on method
        switch ($this->withdrawalMethod) {
            case 'bank_transfer':
                $this->validate([
                    'methodDetails.bank_name' => 'required|string|max:100',
                    'methodDetails.account_number' => 'required|string|max:30',
                    'methodDetails.account_name' => 'required|string|max:100',
                ]);
                break;
            case 'mobile_money':
                $this->validate([
                    'methodDetails.provider' => 'required|in:mtn,airtel',
                    'methodDetails.phone_number' => 'required|string|max:15',
                    'methodDetails.account_name' => 'required|string|max:100',
                ]);
                break;
            case 'paypal':
                $this->validate([
                    'methodDetails.email' => 'required|email',
                    'methodDetails.account_name' => 'required|string|max:100',
                ]);
                break;
        }

        DB::transaction(function () {
            // Calculate fee and net amount
            $fee = ($this->requestAmount * $this->withdrawalFeeRate) / 100;
            $netAmount = $this->requestAmount - $fee;

            // Create withdrawal
            $withdrawal = Withdrawal::create([
                'doctor_id' => $this->doctor->id,
                'withdrawal_number' => Withdrawal::generateWithdrawalNumber(),
                'amount' => $this->requestAmount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'method' => $this->withdrawalMethod,
                'method_details' => $this->methodDetails,
                'requested_at' => now(),
            ]);

            // Find eligible payments and attach to withdrawal
            $eligiblePayments = Payment::whereHas('appointment', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            })
                ->where('status', 'completed')
                ->where('completed_at', '<=', now()->subDays(3))
                ->whereDoesntHave('withdrawals')
                ->orderBy('completed_at')
                ->get();

            $totalAttached = 0;
            foreach ($eligiblePayments as $payment) {
                if ($totalAttached + $payment->amount <= $this->requestAmount) {
                    $withdrawal->payments()->attach($payment->id);
                    $totalAttached += $payment->amount;
                }

                if ($totalAttached >= $this->requestAmount) {
                    break;
                }
            }

            // Save withdrawal details to doctor profile for future use
            $this->saveDoctorDetails();

            $this->showRequestModal = false;
            $this->reset(['requestAmount', 'methodDetails']);

            session()->flash('success', 'Withdrawal request submitted successfully! You will receive UGX ' . number_format($netAmount) . ' after fees.');
        });

        $this->loadStatistics();
    }

    public function saveDoctorDetails()
    {
        $doctorData = [];

        switch ($this->withdrawalMethod) {
            case 'bank_transfer':
                $doctorData = [
                    'bank_name' => $this->methodDetails['bank_name'] ?? '',
                    'account_number' => $this->methodDetails['account_number'] ?? '',
                    'account_name' => $this->methodDetails['account_name'] ?? '',
                    'branch_code' => $this->methodDetails['branch_code'] ?? '',
                ];
                break;
            case 'mobile_money':
                $doctorData = [
                    'mobile_money_provider' => $this->methodDetails['provider'] ?? '',
                    'mobile_money_number' => $this->methodDetails['phone_number'] ?? '',
                    'mobile_money_name' => $this->methodDetails['account_name'] ?? '',
                ];
                break;
            case 'paypal':
                $doctorData = [
                    'paypal_email' => $this->methodDetails['email'] ?? '',
                    'paypal_account_name' => $this->methodDetails['account_name'] ?? '',
                ];
                break;
        }

        if (!empty($doctorData)) {
            $this->doctor->update($doctorData);
        }
    }

    public function cancelWithdrawal($id)
    {
        $withdrawal = Withdrawal::where('doctor_id', $this->doctor->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $withdrawal->update(['status' => 'cancelled']);

        session()->flash('success', 'Withdrawal request cancelled.');
        $this->loadStatistics();
    }

    public function getWithdrawals()
    {
        $query = Withdrawal::where('doctor_id', $this->doctor->id)
            ->with(['payments.appointment.patient'])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate($this->perPage);
    }

    public function getRecentTransactions()
    {
        return Withdrawal::where('doctor_id', $this->doctor->id)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getWithdrawalStats()
    {
        return [
            'total_withdrawn' => Withdrawal::where('doctor_id', $this->doctor->id)
                ->where('status', 'completed')
                ->sum('net_amount'),
            'pending_count' => Withdrawal::where('doctor_id', $this->doctor->id)
                ->whereIn('status', ['pending', 'processing'])
                ->count(),
            'completed_count' => Withdrawal::where('doctor_id', $this->doctor->id)
                ->where('status', 'completed')
                ->count(),
            'total_fees' => Withdrawal::where('doctor_id', $this->doctor->id)
                ->where('status', 'completed')
                ->sum('fee'),
        ];
    }

    public function render()
    {
        $withdrawalStats = $this->getWithdrawalStats();
        $recentTransactions = $this->getRecentTransactions();

        return view('livewire.doctor.withdrawal', [
            'withdrawals' => $this->getWithdrawals(),
            'withdrawalStats' => $withdrawalStats,
            'recentTransactions' => $recentTransactions,
        ])->layout('layouts.doctor', [
            'title' => 'Withdrawals',
            'todayAppointmentsCount' => $this->doctor->appointments()
                ->whereDate('appointment_date', today())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'monthlyEarnings' => Payment::whereHas('appointment', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            })
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->sum('amount'),
            'recentNotifications' => Notification::where('user_id', auth()->id())
                ->latest('sent_at')
                ->limit(5)
                ->get(),
            'unreadNotificationsCount' => Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count()
        ]);
    }
}
