<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Prescription;

class PdfService
{
    public function generatePrescriptionPdf(Prescription $prescription)
    {
        $data = [
            'prescription' => $prescription,
            'medications' => json_decode($prescription->medications, true),
            'doctor' => $prescription->doctor,
            'patient' => $prescription->patient,
        ];

        $pdf = Pdf::loadView('pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->output();
    }
}
