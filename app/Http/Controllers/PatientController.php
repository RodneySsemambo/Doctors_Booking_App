<?php

// ==================== PatientController.php ====================
// app/Http/Controllers/PatientController.php

namespace App\Http\Controllers;

use App\Services\PatientService;
use Exception;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Get patient profile
     * Used in: Profile page
     */
    public function getProfile()
    {
        try {
            $patient = $this->patientService->getPatientDetails(auth()->user()->patient->id);

            return response()->json([
                'success' => true,
                'data' => $patient,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update patient profile
     * Used in: Profile edit component
     */
    public function updateProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone_number' => 'sometimes|string',
                'date_of_birth' => 'sometimes|date',
                'gender' => 'sometimes|in:male,female,other',
                'city' => 'sometimes|string',
                'address' => 'sometimes|string',
                'state' => 'sometimes|string',
                'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $patient = $this->patientService->updatePatient(
                auth()->user()->patient->id,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $patient,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     * Used in: Dashboard component
     */
    public function getDashboardStats()
    {
        try {
            $stats = $this->patientService->getPatientStats(auth()->user()->patient->id);

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment details
     * Used in: Appointments list component
     */
    public function getAppointments(Request $request)
    {
        try {
            $filters = $request->only([
                'status',
                'from_date',
                'to_date',
                'doctor_id',
                'per_page'
            ]);

            $result = $this->patientService->getAppointmentDetails(
                auth()->user()->patient->id,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch appointments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming appointments
     * Used in: Dashboard widget
     */
    public function getUpcomingAppointments(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);

            $appointments = $this->patientService->getUpcomingAppointments(
                auth()->user()->patient->id,
                $limit
            );

            return response()->json([
                'success' => true,
                'data' => $appointments,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming appointments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medical history
     * Used in: Medical history component
     */
    public function getMedicalHistory()
    {
        try {
            $history = $this->patientService->getMedicalHistory(auth()->user()->patient->id);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch medical history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update medical information
     * Used in: Medical info edit component
     */
    public function updateMedicalInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'blood_group' => 'sometimes|string',
                'allergies' => 'sometimes|array',
                'medical_history' => 'sometimes|array',
                'current_medications' => 'sometimes|array',
            ]);

            $patient = $this->patientService->updateMedicalInfo(
                auth()->user()->patient->id,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Medical information updated successfully',
                'data' => $patient,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update medical information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prescription history
     * Used in: Prescriptions component
     */
    public function getPrescriptions(Request $request)
    {
        try {
            $filters = $request->only([
                'from_date',
                'to_date',
                'doctor_id',
                'per_page'
            ]);

            $result = $this->patientService->getPrescriptionHistory(
                auth()->user()->patient->id,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch prescriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     * Used in: Payments component
     */
    public function getPayments(Request $request)
    {
        try {
            $filters = $request->only([
                'status',
                'from_date',
                'to_date',
                'per_page'
            ]);

            $result = $this->patientService->getPaymentHistory(
                auth()->user()->patient->id,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activity
     * Used in: Dashboard activity feed
     */
    public function getRecentActivity(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $activities = $this->patientService->getRecentActivity(
                auth()->user()->patient->id,
                $limit
            );

            return response()->json([
                'success' => true,
                'data' => $activities,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search appointments
     * Used in: Search component
     */
    public function searchAppointments(Request $request)
    {
        try {
            $request->validate([
                'search' => 'required|string|min:2',
            ]);

            $results = $this->patientService->searchAppointments(
                auth()->user()->patient->id,
                $request->search
            );

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
