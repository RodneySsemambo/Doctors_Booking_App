<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cancellations Report - HealthCare</title>
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
            border-bottom: 3px solid #EF4444;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #EF4444;
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
        .alert-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-box .total {
            font-size: 32px;
            font-weight: bold;
            color: #EF4444;
            margin-bottom: 5px;
        }
        .alert-box .label {
            color: #991B1B;
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
        .cancelled-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            background: #FEE2E2;
            color: #991B1B;
        }
        .reason-text {
            font-size: 9px;
            color: #6B7280;
            font-style: italic;
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
        <div class="subtitle">Appointment Cancellations Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <table>
            <tr>
                <td width="50%"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($date_to)->format('M d, Y') }}</td>
                <td width="50%"><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Total Cancellations:</strong> {{ $total }}</td>
                <td><strong>Report Type:</strong> Cancellation Analysis</td>
            </tr>
        </table>
    </div>

    <!-- Alert Box -->
    <div class="alert-box">
        <div class="total">{{ $total }}</div>
        <div class="label">Total Appointments Cancelled During This Period</div>
    </div>

    <!-- Cancellations Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="20%">Patient</th>
                <th width="20%">Doctor</th>
                <th width="15%">Appointment Date</th>
                <th width="10%">Time</th>
                <th width="20%">Reason</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cancellations as $i => $cancel)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cancel->patient->first_name ?? 'N/A' }}</td>
                <td>Dr. {{ $cancel->doctor->first_name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($cancel->appointment_date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cancel->appointment_time)->format('h:i A') }}</td>
                <td>
                    <div class="reason-text">
                        {{ $cancel->cancellation_reason ?? 'No reason provided' }}
                    </div>
                </td>
                <td>
                    <span class="cancelled-badge">
                        Cancelled
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>HealthCare - Appointment Management System | Confidential Report</p>
        <p>Cancellation data helps improve service quality and scheduling</p>
    </div>
</body>
</html>