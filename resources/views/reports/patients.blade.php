<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Statistics Report - HealthCare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid #EC4899;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #EC4899;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .report-meta {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .report-meta table {
            width: 100%;
        }
        .report-meta td {
            padding: 5px 0;
        }
        .summary-box {
            background: #FDF2F8;
            border-left: 4px solid #EC4899;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box .total {
            font-size: 32px;
            font-weight: bold;
            color: #EC4899;
            margin-bottom: 5px;
        }
        .summary-box .label {
            color: #9CA3AF;
            font-size: 11px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data-table thead {
            background: #1F2937;
            color: white;
        }
        table.data-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }
        table.data-table tbody tr:nth-child(even) {
            background: #F9FAFB;
        }
        table.data-table td {
            padding: 10px 8px;
        }
        .gender-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
        }
        .gender-male { background: #DBEAFE; color: #1E40AF; }
        .gender-female { background: #FCE7F3; color: #BE185D; }
        .gender-other { background: #E5E7EB; color: #374151; }
        .blood-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            background: #FEE2E2;
            color: #991B1B;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>HealthCare</h1>
        <div class="subtitle">Patient Statistics Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <table>
            <tr>
                <td width="50%"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($date_to)->format('M d, Y') }}</td>
                <td width="50%"><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>New Registrations:</strong> {{ $total_patients }}</td>
                <td><strong>Report Type:</strong> Demographics</td>
            </tr>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <div class="total">{{ $total_patients }}</div>
        <div class="label">Total New Patients Registered in This Period</div>
    </div>

    <!-- Patients Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="25%">Patient Name</th>
                <th width="10%">Gender</th>
                <th width="12%">Blood Group</th>
                <th width="15%">Contact</th>
                <th width="13%">Registered</th>
                <th width="10%">Visits</th>
                <th width="10%">Confirmed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $i => $patient)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $patient->first_name }}</strong></td>
                <td>
                    <span class="gender-badge gender-{{ $patient->gender }}">
                        {{ ucfirst($patient->gender) }}
                    </span>
                </td>
                <td>
                    <span class="blood-badge">
                        {{ $patient->blood_group ?? 'N/A' }}
                    </span>
                </td>
                <td style="font-size: 9px;">{{ $patient->user->phone ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($patient->created_at)->format('M d, Y') }}</td>
                <td>{{ $patient->appointments->count() }}</td>
                <td>{{ $patient->appointments->where('status', 'confirmed')->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>HealthCare - Patient Management System | Confidential Report</p>
        <p>Patient information is protected under medical privacy regulations</p>
    </div>
</body>
</html>