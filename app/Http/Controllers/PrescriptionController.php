<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Display a listing of the prescriptions.
     */
    public function index()
    {
        return view('patient.prescriptions.index');
    }

    /**
     * Display the specified prescription.
     */
    public function show($id)
    {
        try {
            $prescription = $this->prescriptionService->getPrescriptionById($id);

            // Check if prescription belongs to authenticated patient
            if ($prescription->patient_id !== Auth::user()->patient->id) {
                return redirect()->route('patient.prescriptions.index')
                    ->with('error', 'You are not authorized to view this prescription.');
            }

            return view('patient.prescriptions.show', compact('prescription'));
        } catch (\Exception $e) {
            return redirect()->route('patient.prescriptions.index')
                ->with('error', 'Prescription not found: ' . $e->getMessage());
        }
    }

    /**
     * Print the specified prescription.
     */
    public function print($id)
    {
        try {
            $prescription = $this->prescriptionService->getPrescriptionById($id);

            // Check if prescription belongs to authenticated patient
            if ($prescription->patient_id !== Auth::user()->patient->id) {
                return redirect()->route('patient.prescriptions.index')
                    ->with('error', 'You are not authorized to print this prescription.');
            }

            return view('patient.prescriptions.print', compact('prescription'));
        } catch (\Exception $e) {
            return redirect()->route('patient.prescriptions.index')
                ->with('error', 'Prescription not found: ' . $e->getMessage());
        }
    }

    /**
     * Download prescription as PDF.
     */
    public function download($id)
    {
        try {
            $prescription = $this->prescriptionService->getPrescriptionById($id);

            // Check if prescription belongs to authenticated patient
            if ($prescription->patient_id !== Auth::user()->patient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to download this prescription.'
                ], 403);
            }

            // Check if PDF service exists
            if (!class_exists('App\Services\PdfService')) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF service not available.'
                ], 500);
            }

            $pdfService = app('App\Services\PdfService');
            $pdf = $pdfService->generatePrescriptionPdf($prescription);

            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf;
                },
                "prescription-{$prescription->prescription_number}.pdf",
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment',
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download prescription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prescription statistics.
     */
    public function stats()
    {
        try {
            if (!Auth::user()->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $stats = $this->prescriptionService->getPatientPrescriptionStats(
                Auth::user()->patient->id
            );

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search prescriptions.
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->input('search', '');
            $status = $request->input('status', 'all');

            if (!Auth::user()->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $prescriptions = $this->prescriptionService->getPatientPrescriptions(
                Auth::user()->patient->id,
                $status
            );

            // Apply search filter
            if (!empty($searchTerm)) {
                $searchTerm = strtolower($searchTerm);
                $prescriptions = $prescriptions->filter(function ($prescription) use ($searchTerm) {
                    return str_contains(strtolower($prescription->prescription_number), $searchTerm) ||
                        str_contains(strtolower($prescription->diagnosis), $searchTerm) ||
                        (isset($prescription->doctor->full_name) &&
                            str_contains(strtolower($prescription->doctor->full_name), $searchTerm));
                });
            }

            return response()->json([
                'success' => true,
                'data' => $prescriptions->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
