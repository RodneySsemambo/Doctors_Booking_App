<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prescription #{{ $prescription->id }} - HealthCare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1F2937;
            line-height: 1.6;
            padding: 40px;
        }

        .prescription-header {
    width: 95%;
    padding: 20px;
    background-color: #2563EB; /* NO gradients */
    color: white;
    margin-bottom: 30px;
}
.rx-symbol {
    float: left;
    width: 80px;
    font-size: 48px;
    font-weight: bold;
}
.header-info {
    float: left;
}
.prescription-number {
    float: right;
    text-align: right;
    font-size: 14px;
}


        /* Header with Rx Symbol */
       
        .header-info h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header-info .subtitle {
            font-size: 12px;
            opacity: 0.9;
        }
        

        /* Patient & Doctor Info Cards */
        .info-cards {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-card {
            display: table-cell;
            width: 50%;
            padding: 20px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }
        .info-card:first-child {
            border-right: none;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .info-card:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .card-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #6B7280;
            font-weight: 600;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3B82F6;
        }
        .card-content {
            font-size: 11px;
            line-height: 1.8;
        }
        .patient-name, .doctor-name {
            font-size: 14px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 8px;
        }

        /* Diagnosis Section */
        .diagnosis-section {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .diagnosis-section h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #92400E;
            margin-bottom: 8px;
        }
        .diagnosis-section p {
            color: #78350F;
            font-size: 12px;
        }

        /* Medications Table */
        .medications-section {
            margin-bottom: 30px;
        }
        .section-header {
            background: #1F2937;
            color: white;
            padding: 12px 15px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .medications-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .medications-table thead {
            background: #F3F4F6;
        }
        .medications-table th {
            padding: 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #6B7280;
            font-weight: 600;
            border-bottom: 2px solid #E5E7EB;
        }
        .medications-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #E5E7EB;
        }
        .medication-name {
            font-weight: 600;
            color: #1F2937;
            font-size: 12px;
        }
        .medication-details {
            color: #6B7280;
            font-size: 10px;
            margin-top: 4px;
        }

        /* Instructions */
        .instructions-section {
            background: #EFF6FF;
            border-left: 4px solid #3B82F6;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .instructions-section h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #1E40AF;
            margin-bottom: 10px;
        }
        .instructions-section p {
            color: #1F2937;
            font-size: 11px;
            line-height: 1.8;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 20px 0;
        }
        .signature-line {
            border-top: 2px solid #1F2937;
            padding-top: 10px;
            margin-top: 40px;
            max-width: 250px;
        }
        .signature-label {
            font-size: 10px;
            color: #6B7280;
            text-transform: uppercase;
        }
        .doctor-signature {
            font-weight: 600;
            color: #1F2937;
        }

        /* Footer */
        .prescription-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
        }
        .verification-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #D1FAE5;
            color: #065F46;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .footer-text {
            color: #6B7280;
            font-size: 9px;
            line-height: 1.8;
        }

        /* Warning Box */
        .warning-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .warning-box p {
            color: #991B1B;
            font-size: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
   <div class="prescription-header">
    
        <h1>Medical Prescription</h1>
        <div class="subtitle">HealthCare Professional Medical Services</div>
   
    
    

</div>


    <!-- Patient & Doctor Info -->
    <div class="info-cards">
        <div class="info-card">
            <div class="card-title">Patient Information</div>
            <div class="card-content">
                <div class="patient-name">
                    {{ $prescription->appointment->patient->first_name }}
                    {{ $prescription->appointment->patient->last_name ?? '' }}
                </div>
                <strong>Gender:</strong> {{ ucfirst($prescription->appointment->patient->gender) }}<br>
                <strong>Blood Group:</strong> {{ $prescription->appointment->patient->blood_group ?? 'N/A' }}<br>
                <strong>Phone:</strong> {{ $prescription->appointment->patient->user->phone }}
            </div>
        </div>
        <div class="info-card">
            <div class="card-title">Prescribing Doctor</div>
            <div class="card-content">
                <div class="doctor-name">
                    Dr. {{ $prescription->appointment->doctor->first_name }}
                    {{ $prescription->appointment->doctor->last_name ?? '' }}
                </div>
                <strong>Specialization:</strong> {{ $prescription->appointment->doctor->specialization->name ?? 'General Practice' }}<br>
                <strong>License No:</strong> {{ $prescription->appointment->doctor->license_number ?? 'N/A' }}<br>
                <strong>Hospital:</strong> {{ $prescription->appointment->doctor->hospitals ?? 'N/A' }}<br>
                <strong>Contact:</strong> {{ $prescription->appointment->doctor->user->phone ?? 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Diagnosis -->
    @if($prescription->diagnosis)
    <div class="diagnosis-section">
        <h3>📋 Diagnosis</h3>
        <p>{{ $prescription->diagnosis }}</p>
    </div>
    @endif

    <!-- Medications -->
    <div class="medications-section">
        <div class="section-header">💊 Prescribed Medications</div>
        
        @if(is_array($prescription->medications) && count($prescription->medications) > 0)
        <table class="medications-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">Medication Name</th>
                    <th width="20%">Dosage</th>
                    <th width="20%">Frequency</th>
                    <th width="25%">Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->medications as $index => $med)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="medication-name">{{ $med['name'] ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $med['dosage'] ?? 'N/A' }}</td>
                    <td>{{ $med['frequency'] ?? 'N/A' }}</td>
                    <td>{{ $med['duration'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 20px; text-align: center; background: #F9FAFB; border-radius: 4px;">
            <p style="color: #6B7280;">{{ $prescription->medications ?? 'No medications prescribed' }}</p>
        </div>
        @endif
    </div>

    <!-- Instructions -->
    @if($prescription->instructions)
    <div class="instructions-section">
        <h3>📝 Special Instructions</h3>
        <p>{{ $prescription->instructions }}</p>
    </div>
    @endif

    <!-- Warning -->
    <div class="warning-box">
        <p>⚠️ IMPORTANT: Take medications as prescribed. Do not stop or change dosage without consulting your doctor.</p>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-label">Doctor's Signature</div>
                <div class="doctor-signature">
                    Dr. {{ $prescription->appointment->doctor->first_name }}
                    {{ $prescription->appointment->doctor->last_name ?? '' }}
                </div>
            </div>
        </div>
        <div class="signature-box" style="text-align: right;">
            <div class="signature-line" style="margin-left: auto;">
                <div class="signature-label">Date Issued</div>
                <div class="doctor-signature">
                    {{ now()->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="prescription-footer">
        <div class="verification-badge">
            ✓ Digitally Verified Prescription
        </div>
        <div class="footer-text">
            This prescription is computer-generated and valid without a physical signature.<br>
            For verification, contact HealthCare at info@healthcare.com or +256 XXX XXX XXX<br>
            &copy; {{ date('Y') }} HealthCare - All Rights Reserved
        </div>
    </div>
</body>
</html>