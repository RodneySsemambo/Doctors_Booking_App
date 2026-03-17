<?php

namespace App\Livewire\Patient;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Services\MedicalHistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalHistory extends Component
{
    use WithPagination, WithFileUploads;

    // State properties
    public $searchTerm = '';
    public $recordTypeFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';

    // Modal properties
    public $showViewModal = false;
    public $showUploadModal = false;
    public $selectedRecord = null;

    // Upload properties
    public $uploadTitle = '';
    public $uploadDescription = '';
    public $uploadRecordType = 'consultation_note';
    public $uploadRecordDate = '';
    public $uploadFile = null;
    public $uploadAppointmentId = null;

    // Service instance
    protected $medicalHistoryService;

    // Statistics
    public $stats = [
        'total' => 0,
        'lab_results' => 0,
        'consultation_notes' => 0,
        'vaccinations' => 0,
        'imaging' => 0,
    ];

    // Record types
    public $recordTypes = [];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'recordTypeFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function __construct()
    {
        $this->medicalHistoryService = app(MedicalHistoryService::class);
    }

    public function mount()
    {
        $this->recordTypes = $this->medicalHistoryService->getRecordTypes();
        $this->loadStatistics();
        $this->uploadRecordDate = now()->format('Y-m-d');
    }

    public function loadStatistics()
    {
        if (Auth::check() && Auth::user()->patient) {
            $this->stats = $this->medicalHistoryService->getPatientMedicalRecordStats(
                Auth::user()->patient->id
            );
        }
    }

    public function getMedicalRecordsProperty()
    {
        if (!Auth::check() || !Auth::user()->patient) {
            return collect();
        }

        $filters = [];

        if ($this->recordTypeFilter !== 'all') {
            $filters['record_type'] = $this->recordTypeFilter;
        }

        if ($this->searchTerm) {
            $filters['search'] = $this->searchTerm;
        }

        if ($this->dateFrom) {
            $filters['date_from'] = $this->dateFrom;
        }

        if ($this->dateTo) {
            $filters['date_to'] = $this->dateTo;
        }

        return $this->medicalHistoryService->getPatientMedicalRecords(
            Auth::user()->patient->id,
            $filters
        );
    }

    public function getTimelineProperty()
    {
        if (!Auth::check() || !Auth::user()->patient) {
            return collect();
        }

        return $this->medicalHistoryService->getPatientMedicalTimeline(
            Auth::user()->patient->id,
            5
        );
    }

    public function viewRecord($recordId)
    {
        try {
            $this->selectedRecord = $this->medicalHistoryService->getMedicalRecordById($recordId);

            // Verify the record belongs to the current patient
            if ($this->selectedRecord->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to view this record.');
                return;
            }

            $this->showViewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Medical record not found: ' . $e->getMessage());
        }
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedRecord = null;
    }

    public function openUploadModal()
    {
        $this->showUploadModal = true;
        $this->resetUploadForm();
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->resetUploadForm();
    }

    public function resetUploadForm()
    {
        $this->uploadTitle = '';
        $this->uploadDescription = '';
        $this->uploadRecordType = 'consultation_note';
        $this->uploadRecordDate = now()->format('Y-m-d');
        $this->uploadFile = null;
        $this->uploadAppointmentId = null;
        $this->resetErrorBag();
    }

    public function uploadRecord()
    {
        $this->validate([
            'uploadTitle' => 'required|string|max:255',
            'uploadDescription' => 'required|string|min:10',
            'uploadRecordType' => 'required|in:lab_result,consultation_note,vaccination,imaging',
            'uploadRecordDate' => 'required|date|before_or_equal:today',
            'uploadFile' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            if (!Auth::user()->patient) {
                throw new \Exception('Patient profile not found');
            }

            $data = [
                'patient_id' => Auth::user()->patient->id,
                'appointment_id' => $this->uploadAppointmentId,
                'record_type' => $this->uploadRecordType,
                'title' => $this->uploadTitle,
                'description' => $this->uploadDescription,
                'recorded_by' => Auth::id(),
                'recorded_date' => $this->uploadRecordDate,
            ];

            $medicalRecord = $this->medicalHistoryService->createMedicalRecord(
                $data,
                $this->uploadFile
            );

            $this->closeUploadModal();
            $this->loadStatistics();

            session()->flash('success', 'Medical record uploaded successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload record: ' . $e->getMessage());
        }
    }

    public function downloadFile($recordId)
    {
        try {
            $fileInfo = $this->medicalHistoryService->downloadMedicalRecordFile($recordId);

            // Verify ownership
            $record = $this->medicalHistoryService->getMedicalRecordById($recordId);
            if ($record->patient_id !== Auth::user()->patient->id) {
                throw new \Exception('You are not authorized to download this file');
            }

            return response()->streamDownload(function () use ($fileInfo) {
                echo Storage::get($fileInfo['path']);
            }, $fileInfo['name'], [
                'Content-Type' => $fileInfo['mime_type'],
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download file: ' . $e->getMessage());
        }
    }

    public function deleteRecord($recordId)
    {
        try {
            // Verify ownership
            $record = $this->medicalHistoryService->getMedicalRecordById($recordId);
            if ($record->patient_id !== Auth::user()->patient->id) {
                throw new \Exception('You are not authorized to delete this record');
            }

            $this->medicalHistoryService->deleteMedicalRecord($recordId);
            $this->loadStatistics();

            session()->flash('success', 'Medical record deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedRecordTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.patient.medical-history', [
            'medicalRecords' => $this->medicalRecords,
            'timeline' => $this->timeline,
        ])
            ->layout('layouts.patient');
    }
}
