<?php

namespace App\Livewire\Patient;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\PatientService;
use App\Models\Patient;

class Dashboard extends Component
{
    public array $stats = [];
    public $patient;
    public $upcomingAppointments = null;
    public $recentActivity = null;
    public $patientInfo = null;

    public function mount(PatientService $patientService)
    {
        try {
            // Get authenticated user
            $userId = Auth::id();

            if (!$userId) {
                Log::error('Dashboard: User not authenticated');
                return redirect()->route('login');
            }

            // Get patient record using user_id
            $patient = Patient::where('user_id', $userId)->first();

            if (!$patient) {
                Log::error("Dashboard: Patient record not found for user_id: {$userId}");
                session()->flash('error', 'Patient profile not found. Please complete your registration.');
                return redirect()->route('patient.profile');
            }

            $this->patientInfo = $patient;

            Log::info("Dashboard: Loading data for patient_id: {$patient->id}");

            // Get comprehensive dashboard statistics
            $dashboardStats = $patientService->getPatientStats($patient->id);

            // Set stats for the view
            $this->stats = [
                'total' => $dashboardStats['appointments']['total'],
                'upcoming' => $dashboardStats['appointments']['upcomingAppointments'],
                'completed' => $dashboardStats['appointments']['completedAppointments'],
                'cancelled' => $dashboardStats['appointments']['cancelledAppointments'],
                'pending' => $dashboardStats['appointments']['pendingAppointments'],
            ];

            // Get upcoming appointments (limit 5 for dashboard)
            $this->upcomingAppointments = $patientService->getUpcomingAppointments($patient->id, 5);

            // Get recent activity
            $this->recentActivity = $dashboardStats['recent_activity'];

            Log::info("Dashboard: Data loaded successfully", [
                'stats' => $this->stats,
                'upcoming_count' => $this->upcomingAppointments->count(),
                'activity_count' => $this->recentActivity->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Unable to load dashboard data. Please try again.');

            // Set default empty values
            $this->stats = [
                'total' => 0,
                'upcoming' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'pending' => 0,
            ];
            $this->upcomingAppointments = collect();
            $this->recentActivity = collect();
        }
    }

    public function refreshDashboard(PatientService $patientService)
    {
        try {
            $patient = Patient::where('user_id', Auth::id())->first();

            if ($patient) {
                $dashboardStats = $patientService->getPatientStats($patient->id);

                $this->stats = [
                    'total' => $dashboardStats['appointments']['total'],
                    'upcoming' => $dashboardStats['appointments']['upcomingAppointments'],
                    'completed' => $dashboardStats['appointments']['completedAppointments'],
                    'cancelled' => $dashboardStats['appointments']['cancelledAppointments'],
                    'pending' => $dashboardStats['appointments']['pendingAppointments'],
                ];

                $this->upcomingAppointments = $patientService->getUpcomingAppointments($patient->id, 5);
                $this->recentActivity = $dashboardStats['recent_activity'];

                session()->flash('success', 'Dashboard refreshed successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Dashboard Refresh Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to refresh dashboard.');
        }
    }

    public function render()
    {
        return view('livewire.patient.dashboard')
            ->layout('layouts.patient');
    }
}
