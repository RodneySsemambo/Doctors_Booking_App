<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription - {{ $prescription->prescription_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
            padding: 30px;
        }
        
        .prescription-container {
            border: 2px solid #2563eb;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .header .prescription-number {
            font-size: 14px;
            color: #666;
            font-weight: bold;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        
        .info-column h3 {
            font-size: 14px;
            color: #2563eb;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .info-column p {
            margin: 3px 0;
            font-size: 12px;
        }
        
        .info-column .label {
            color: #666;
            font-size: 11px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
        }
        
        .diagnosis-box,
        .instructions-box {
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin-top: 10px;
        }
        
        .medication-item {
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .medication-item .medication-name {
            font-size: 14px;
            font-weight: bold;
            color: #111;
            margin-bottom: 5px;
        }
        
        .medication-item .medication-detail {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .medication-item .medication-detail strong {
            color: #2563eb;
            display: inline-block;
            width: 80px;
        }
        
        .footer {
            border-top: 2px solid #2563eb;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
        }
        
        .footer p {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        
        .validity-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
        }
        
        .dates-section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f9ff;
            border: 1px solid #bfdbfe;
        }
        
        .dates-section p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="prescription-container">
        <!-- Header -->
        <div class="header">
            <h1>MEDICAL PRESCRIPTION</h1>
            <p class="prescription-number">{{ $prescription->prescription_number }}</p>
        </div>

        <!-- Patient and Doctor Information -->
        <div class="info-section">
            <div class="info-column">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> {{ $prescription->patient->full_name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $prescription->patient->email ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $prescription->patient->phone ?? 'N/A' }}</p>
                @if(isset($prescription->patient->date_of_birth))
                    <p><strong>DOB:</strong> {{ \Carbon\Carbon::parse($prescription->patient->date_of_birth)->format('M d, Y') }}</p>
                @endif
            </div>
            
            <div class="info-column">
                <h3>Prescribed By</h3>
                <p><strong>Name:</strong> Dr. {{ $prescription->doctor->full_name ?? 'N/A' }}</p>
                <p><strong>Specialization:</strong> {{ $prescription->doctor->specialization ?? 'N/A' }}</p>
                <p><strong>License:</strong> {{ $prescription->doctor->license_number ?? 'N/A' }}</p>
                <p><strong>Contact:</strong> {{ $prescription->doctor->phone ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Dates -->
        <div class="dates-section">
            <p><strong>Date Issued:</strong> {{ \Carbon\Carbon::parse($prescription->created_at)->format('F d, Y') }}</p>
            <p><strong>Valid Until:</strong> {{ \Carbon\Carbon::parse($prescription->valid_until)->format('F d, Y') }}</p>
            @if($prescription->is_dispensed)
                <p><strong>Dispensed On:</strong> {{ \Carbon\Carbon::parse($prescription->dispensed_at)->format('F d, Y h:i A') }}</p>
            @endif
        </div>

        <!-- Diagnosis -->
        <div class="section">
            <div class="section-title">Diagnosis</div>
            <div class="diagnosis-box">
                {{ $prescription->diagnosis }}
            </div>
        </div>

        <!-- Medications -->
        <div class="section">
            <div class="section-title">Prescribed Medications</div>
            @foreach($medications as $index => $medication)
                <div class="medication-item">
                    <div class="medication-name">
                        ℞ {{ $index + 1 }}. {{ $medication['name'] }}
                    </div>
                    <div class="medication-detail">
                        <strong>Dosage:</strong> {{ $medication['dosage'] }}
                    </div>
                    @if(isset($medication['frequency']))
                        <div class="medication-detail">
                            <strong>Frequency:</strong> {{ $medication['frequency'] }}
                        </div>
                    @endif
                    @if(isset($medication['duration']))
                        <div class="medication-detail">
                            <strong>Duration:</strong> {{ $medication['duration'] }}
                        </div>
                    @endif
                    @if(isset($medication['instructions']))
                        <div class="medication-detail">
                            <strong>Instructions:</strong> {{ $medication['instructions'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- General Instructions -->
        <div class="section">
            <div class="section-title">General Instructions</div>
            <div class="instructions-box">
                {{ $prescription->instructions }}
            </div>
        </div>

        <!-- Validity Notice -->
        <div class="validity-notice">
            <strong>⚠ Important:</strong> This prescription is valid until {{ \Carbon\Carbon::parse($prescription->valid_until)->format('F d, Y') }}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>For any questions or concerns, please contact your healthcare provider.</p>
            <p>This is a computer-generated prescription and does not require a physical signature.</p>
            <p style="margin-top: 15px; font-size: 10px;">Generated on {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>