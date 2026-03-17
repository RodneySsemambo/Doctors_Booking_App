<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MedicalHistoryService
{
    /**
     * Create a new medical record
     */
    public function createMedicalRecord(array $data, ?UploadedFile $file = null): MedicalRecord
    {
        DB::beginTransaction();

        try {
            // Verify appointment belongs to patient
            $appointment = Appointment::findOrFail($data['appointment_id']);

            if ($appointment->patient_id != $data['patient_id']) {
                throw new Exception('Appointment does not belong to this patient');
            }

            // Handle file upload if present
            if ($file) {
                $data['file_path'] = $this->storeFile($file, $data['record_type']);
                $data['file_type'] = $file->getClientOriginalExtension();
            }

            // Set recorded date if not provided
            if (!isset($data['recorded_date'])) {
                $data['recorded_date'] = Carbon::now();
            }

            $medicalRecord = MedicalRecord::create($data);

            DB::commit();

            session()->flash('success', 'Medical record created successfully.');

            return $medicalRecord->load(['appointment', 'recordedBy']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store uploaded file
     */
    private function storeFile(UploadedFile $file, string $recordType): string
    {
        $directory = "medical-records/{$recordType}/" . date('Y/m');

        return $file->store($directory, 'public');
    }

    /**
     * Get patient's medical records
     */
    public function getPatientMedicalRecords(int $patientId, array $filters = []): \Illuminate\Support\Collection
    {
        $query = MedicalRecord::with(['appointment', 'recordedBy'])
            ->where('patient_id', $patientId)
            ->orderBy('recorded_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['record_type'])) {
            $query->where('record_type', $filters['record_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('recorded_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('recorded_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get medical record by ID
     */
    public function getMedicalRecordById(int $id): MedicalRecord
    {
        return MedicalRecord::with(['appointment', 'recordedBy', 'patient'])
            ->findOrFail($id);
    }

    /**
     * Update medical record
     */
    public function updateMedicalRecord(int $id, array $data, ?UploadedFile $file = null): MedicalRecord
    {
        DB::beginTransaction();

        try {
            $medicalRecord = MedicalRecord::findOrFail($id);

            // Handle file upload if present
            if ($file) {
                // Delete old file if exists
                if ($medicalRecord->file_path && Storage::exists('public/' . $medicalRecord->file_path)) {
                    Storage::delete('public/' . $medicalRecord->file_path);
                }

                $data['file_path'] = $this->storeFile($file, $data['record_type'] ?? $medicalRecord->record_type);
                $data['file_type'] = $file->getClientOriginalExtension();
            }

            $medicalRecord->update($data);

            DB::commit();

            return $medicalRecord->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete medical record
     */
    public function deleteMedicalRecord(int $id): bool
    {
        DB::beginTransaction();

        try {
            $medicalRecord = MedicalRecord::findOrFail($id);

            // Delete associated file
            if ($medicalRecord->file_path && Storage::exists('public/' . $medicalRecord->file_path)) {
                Storage::delete('public/' . $medicalRecord->file_path);
            }

            $medicalRecord->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get medical record statistics for patient
     */
    public function getPatientMedicalRecordStats(int $patientId): array
    {
        return [
            'total' => MedicalRecord::where('patient_id', $patientId)->count(),
            'lab_results' => MedicalRecord::where('patient_id', $patientId)
                ->where('record_type', 'lab_result')
                ->count(),
            'consultation_notes' => MedicalRecord::where('patient_id', $patientId)
                ->where('record_type', 'consultation_note')
                ->count(),
            'vaccinations' => MedicalRecord::where('patient_id', $patientId)
                ->where('record_type', 'vaccination')
                ->count(),
            'imaging' => MedicalRecord::where('patient_id', $patientId)
                ->where('record_type', 'imaging')
                ->count(),
        ];
    }

    /**
     * Get record types
     */
    public function getRecordTypes(): array
    {
        return [
            'lab_result' => 'Lab Result',
            'consultation_note' => 'Consultation Note',
            'vaccination' => 'Vaccination',
            'imaging' => 'Imaging',
        ];
    }

    /**
     * Get timeline of medical records
     */
    public function getPatientMedicalTimeline(int $patientId, int $limit = 10): \Illuminate\Support\Collection
    {
        return MedicalRecord::with(['appointment', 'recordedBy'])
            ->where('patient_id', $patientId)
            ->orderBy('recorded_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'title' => $record->title,
                    'type' => $record->record_type,
                    'type_label' => $record->record_type_label,
                    'type_color' => $record->record_type_color,
                    'type_icon' => $record->record_type_icon,
                    'date' => $record->recorded_date->format('Y-m-d'),
                    'formatted_date' => $record->recorded_date->format('M d, Y'),
                    'description' => $record->description,
                    'has_file' => !empty($record->file_path),
                    'recorded_by' => $record->recordedBy->name ?? 'Unknown',
                ];
            });
    }

    /**
     * Download medical record file
     */
    public function downloadMedicalRecordFile(int $id): array
    {
        $medicalRecord = MedicalRecord::findOrFail($id);

        if (!$medicalRecord->file_path) {
            throw new Exception('No file attached to this record');
        }

        $filePath = 'public/' . $medicalRecord->file_path;

        if (!Storage::exists($filePath)) {
            throw new Exception('File not found');
        }

        return [
            'path' => $filePath,
            'name' => $this->generateDownloadFileName($medicalRecord),
            'mime_type' => Storage::mimeType($filePath),
        ];
    }

    /**
     * Generate download file name
     */
    private function generateDownloadFileName(MedicalRecord $record): string
    {
        $name = str_replace(' ', '_', $record->title);
        $type = $record->record_type;
        $date = $record->recorded_date->format('Y-m-d');
        $extension = $record->file_type ?: 'pdf';

        return "{$name}_{$type}_{$date}.{$extension}";
    }
}
