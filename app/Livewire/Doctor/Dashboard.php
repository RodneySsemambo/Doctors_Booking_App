<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Dashboard extends Component
{
    public $doctor;
    public $stats = [];
    public $todayAppointments = [];
    public $upcomingAppointments = [];
    public $recentNotifications = [];
    public $unreadNotificationsCount = 0;
    public $selectedDate;
    public $appointmentStats = [];

    // Add these for layout
    public $todayAppointmentsCount;
    public $monthlyEarnings;

    public function mount()
    {
        $this->doctor = Auth::user()->doctor;

        if (!$this->doctor) {
            abort(403, 'Doctor profile not found. Please contact support.');
        }

        Log::info('DOCTOR DASHBOARD MOUNTED', [
            'user_type' => auth()->user()?->user_type,
            'user_id' => Auth::id(),
        ]);

        $this->selectedDate = now()->format('Y-m-d');
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loadStats();
        $this->loadTodayAppointments();
        $this->loadUpcomingAppointments();
        $this->loadNotifications();
        $this->loadAppointmentStats();
        $this->loadLayoutData(); // Add this
    }

    protected function loadLayoutData()
    {
        // Data needed for the layout
        $this->todayAppointmentsCount = $this->doctor->appointments()
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $this->monthlyEarnings = Payment::whereHas('appointment', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('amount');
    }

    protected function loadStats()
    {
        $this->stats = [
            'total_appointments' => $this->doctor->appointments()->count(),
            'today_appointments' => $this->doctor->appointments()
                ->whereDate('appointment_date', today())
                ->count(),
            'upcoming_appointments' => $this->doctor->appointments()
                ->where('appointment_date', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'total_patients' => $this->doctor->appointments()
                ->distinct('patient_id')
                ->count('patient_id'),
            'total_earnings' => Payment::whereHas('appointment', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            })
                ->where('status', 'completed')
                ->sum('amount'),
            'this_month_earnings' => $this->monthlyEarnings, // Use the property
        ];
    }

    protected function loadTodayAppointments()
    {
        $this->todayAppointments = $this->doctor->appointments()
            ->with(['patient.user'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();
    }

    protected function loadUpcomingAppointments()
    {
        $this->upcomingAppointments = $this->doctor->appointments()
            ->with(['patient.user'])
            ->where('appointment_date', '>', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();
    }

    protected function loadNotifications()
    {
        $this->recentNotifications = Notification::where('user_id', Auth::id())
            ->latest('sent_at')
            ->limit(5)
            ->get();

        $this->unreadNotificationsCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    protected function loadAppointmentStats()
    {
        $this->appointmentStats = $this->doctor->appointments()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    public function confirmAppointment($appointmentId)
    {
        $appointment = $this->doctor->appointments()->findOrFail($appointmentId);

        $appointment->update(['status' => 'confirmed']);

        $this->dispatch('appointment-confirmed');
        $this->loadDashboardData();

        session()->flash('success', 'Appointment confirmed successfully!');
    }

    public function completeAppointment($appointmentId)
    {
        $appointment = $this->doctor->appointments()->findOrFail($appointmentId);

        $appointment->update(['status' => 'completed']);

        $this->dispatch('appointment-completed');
        $this->loadDashboardData();

        session()->flash('success', 'Appointment marked as completed!');
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function markAllNotificationsAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();

        session()->flash('success', 'All notifications marked as read!');
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        session()->flash('success', 'Dashboard refreshed!');
    }

    public function render()
    {
        return view('livewire.doctor.dashboard')
            ->layout('layouts.doctor', [
                'title' => 'Dashboard',
                'todayAppointmentsCount' => $this->todayAppointmentsCount,
                'monthlyEarnings' => $this->monthlyEarnings,
                'recentNotifications' => $this->recentNotifications,
                'unreadNotificationsCount' => $this->unreadNotificationsCount,
            ]);
    }
}
