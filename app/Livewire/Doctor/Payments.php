<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Notification;
use Carbon\Carbon;

class Payments extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = '';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $doctor;

    // Statistics
    public $totalEarnings = 0;
    public $pendingPayments = 0;
    public $completedPayments = 0;
    public $monthlyEarnings = 0;

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Total earnings (all time)
        $this->totalEarnings = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->sum('amount');

        // Pending payments
        $this->pendingPayments = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'pending')
            ->sum('amount');

        // Completed payments count
        $this->completedPayments = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->count();

        // Monthly earnings
        $this->monthlyEarnings = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('amount');
    }

    public function getPayments()
    {
        $query = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->with(['appointment.patient.user'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('payment_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('appointment.patient', function ($q3) {
                            $q3->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                ->orWhereHas('user', function ($q4) {
                                    $q4->where('email', 'like', '%' . $this->search . '%')
                                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                                });
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($q) {
                $q->whereDate('created_at', $this->dateFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function markAsPaid($paymentId)
    {
        $payment = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })->findOrFail($paymentId);

        $payment->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        $this->loadStatistics();
        session()->flash('success', 'Payment marked as completed!');
    }

    public function downloadReceipt($paymentId)
    {
        $payment = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })->with(['appointment.patient.user'])->findOrFail($paymentId);


        session()->flash('success', 'Receipt download initiated for payment #' . $payment->payment_number);
    }

    public function getMonthlyEarningsChart()
    {
        $data = [];
        $months = 6; // Last 6 months

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $year = $date->format('Y');

            $earnings = Payment::whereHas('appointment', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            })
                ->where('status', 'completed')
                ->whereMonth('completed_at', $date->month)
                ->whereYear('completed_at', $date->year)
                ->sum('amount');

            $data[] = [
                'month' => $monthName . ' ' . $date->format('y'),
                'earnings' => $earnings,
                'formatted' => 'UGX ' . number_format($earnings)
            ];
        }

        return $data;
    }

    public function getRecentTransactions()
    {
        return Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->with(['appointment.patient'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $monthlyChartData = $this->getMonthlyEarningsChart();
        $recentTransactions = $this->getRecentTransactions();

        return view('livewire.doctor.payment', [
            'payments' => $this->getPayments(),
            'monthlyChartData' => $monthlyChartData,
            'recentTransactions' => $recentTransactions
        ])->layout('layouts.doctor', [
            'title' => 'Payments & Earnings',
            'todayAppointmentsCount' => $this->doctor->appointments()
                ->whereDate('appointment_date', today())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'monthlyEarnings' => $this->monthlyEarnings,
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
