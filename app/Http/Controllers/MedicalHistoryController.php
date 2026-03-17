<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MedicalHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalHistoryController extends Controller
{
    protected $medicalHistoryService;

    public function __construct(MedicalHistoryService $medicalHistoryService)
    {
        $this->medicalHistoryService = $medicalHistoryService;
    }

    /**
     * Display medical history index page
     */
    public function index()
    {
        return view('livewire.patient.medical-history');
    }

    /**
     * Download medical record file
     */
    public function download($id)
    {
        try {
            $fileInfo = $this->medicalHistoryService->downloadMedicalRecordFile($id);

            // Verify ownership
            $record = $this->medicalHistoryService->getMedicalRecordById($id);
            if ($record->patient_id !== Auth::user()->patient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to download this file'
                ], 403);
            }

            return response()->streamDownload(function () use ($fileInfo) {
                echo Storage::get($fileInfo['path']);
            }, $fileInfo['name'], [
                'Content-Type' => $fileInfo['mime_type'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medical history statistics
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

            $stats = $this->medicalHistoryService->getPatientMedicalRecordStats(
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
     * Get medical timeline
     */
    public function timeline()
    {
        try {
            if (!Auth::user()->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $timeline = $this->medicalHistoryService->getPatientMedicalTimeline(
                Auth::user()->patient->id,
                10
            );

            return response()->json([
                'success' => true,
                'data' => $timeline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load timeline: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medical records
     */
    public function search(Request $request)
    {
        try {
            if (!Auth::user()->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $filters = $request->only(['search', 'record_type', 'date_from', 'date_to']);

            $records = $this->medicalHistoryService->getPatientMedicalRecords(
                Auth::user()->patient->id,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $records
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload medical record (API endpoint)
     */
    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'record_type' => 'required|in:lab_result,consultation_note,vaccination,imaging',
                'recorded_date' => 'required|date|before_or_equal:today',
                'file' => 'nullable|file|max:10240',
                'appointment_id' => 'nullable|exists:appointments,id',
            ]);

            if (!Auth::user()->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $data = array_merge($validated, [
                'patient_id' => Auth::user()->patient->id,
                'recorded_by' => Auth::id(),
            ]);

            $record = $this->medicalHistoryService->createMedicalRecord(
                $data,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' => 'Medical record uploaded successfully',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload record: ' . $e->getMessage()
            ], 500);
        }
    }
}
