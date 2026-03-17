<?php

namespace App\Livewire\Patient\Prescriptions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\PrescriptionService;
use Illuminate\Support\Facades\Auth;

class MyPrescriptions extends Component
{
    use WithPagination;

    // State properties
    public $searchTerm = '';
    public $statusFilter = 'all';

    // Modal properties
    public $showDetailsModal = false;
    public $showPrintModal = false;
    public $selectedPrescription = null;

    // Service instance
    protected $prescriptionService;

    // Statistics
    public $stats = [
        'total' => 0,
        'active' => 0,
        'dispensed' => 0,
        'expired' => 0
    ];

    public function __construct()
    {
        $this->prescriptionService = app(PrescriptionService::class);
    }

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        if (Auth::check() && Auth::user()->patient) {
            $this->stats = $this->prescriptionService->getPatientPrescriptionStats(
                Auth::user()->patient->id
            );
        }
    }

    public function getPrescriptionsProperty()
    {
        if (!Auth::check() || !Auth::user()->patient) {
            return collect();
        }

        $prescriptions = $this->prescriptionService->getPatientPrescriptions(
            Auth::user()->patient->id,
            $this->statusFilter === 'all' ? 'all' : $this->statusFilter
        );

        // Apply search filter
        if (!empty($this->searchTerm)) {
            $searchTerm = strtolower($this->searchTerm);

            $prescriptions = $prescriptions->filter(function ($prescription) use ($searchTerm) {
                return str_contains(strtolower($prescription->prescription_number), $searchTerm) ||
                    str_contains(strtolower($prescription->diagnosis), $searchTerm) ||
                    (isset($prescription->doctor->first_name) &&
                        str_contains(strtolower($prescription->doctor->first_name), $searchTerm));
            });
        }

        return $prescriptions;
    }

    public function viewDetails($prescriptionId)
    {
        try {
            $this->selectedPrescription = $this->prescriptionService->getPrescriptionById($prescriptionId);

            // Verify the prescription belongs to the current patient
            if ($this->selectedPrescription->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to view this prescription.');
                return;
            }

            $this->showDetailsModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Prescription not found: ' . $e->getMessage());
        }
    }

    public function openPrintModal($prescriptionId)
    {
        try {
            $this->selectedPrescription = $this->prescriptionService->getPrescriptionById($prescriptionId);

            // Verify the prescription belongs to the current patient
            if ($this->selectedPrescription->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to print this prescription.');
                return;
            }

            $this->showPrintModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Prescription not found: ' . $e->getMessage());
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPrescription = null;
    }

    public function closePrintModal()
    {
        $this->showPrintModal = false;
        $this->selectedPrescription = null;
    }

    public function downloadPrescription($prescriptionId)
    {
        try {
            $prescription = $this->prescriptionService->getPrescriptionById($prescriptionId);

            // Verify the prescription belongs to the current patient
            if ($prescription->patient_id !== Auth::user()->patient->id) {
                session()->flash('error', 'You are not authorized to download this prescription.');
                return;
            }

            // Generate PDF using your PDF service
            if (class_exists('App\Services\PdfService')) {
                $pdfService = app('App\Services\PdfService');
                $pdf = $pdfService->generatePrescriptionPdf($prescription);

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf;
                }, "prescription-{$prescription->prescription_number}.pdf", [
                    'Content-Type' => 'application/pdf',
                ]);
            } else {
                // Fallback: Redirect to print modal
                $this->openPrintModal($prescriptionId);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download prescription: ' . $e->getMessage());
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

    public function render()
    {
        return view('livewire.patient.prescriptions.my-prescriptions', [
            'prescriptions' => $this->prescriptions,
        ])
            ->layout('layouts.patient');
    }
}
