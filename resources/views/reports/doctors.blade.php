<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Performance Report - HealthCare</title>
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
            border-bottom: 3px solid #8B5CF6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #8B5CF6;
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
        .rating-stars {
            color: #F59E0B;
            font-size: 12px;
        }
        .performance-bar {
            background: #E5E7EB;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .performance-fill {
            background: #8B5CF6;
            height: 100%;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-high { background: #D1FAE5; color: #065F46; }
        .badge-medium { background: #FEF3C7; color: #92400E; }
        .badge-low { background: #FEE2E2; color: #991B1B; }
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
        <div class="subtitle">Doctor Performance Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <table>
            <tr>
                <td width="50%"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($date_to)->format('M d, Y') }}</td>
                <td width="50%"><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Total Doctors:</strong> {{ count($doctors) }}</td>
                <td><strong>Report Type:</strong> Performance Analysis</td>
            </tr>
        </table>
    </div>

    <!-- Doctors Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="25%">Doctor Name</th>
                <th width="20%">Specialization</th>
                <th width="15%">Appointments</th>
                <th width="15%">Performance</th>
                <th width="10%">Rating</th>
                <th width="10%">Status</th>
                <th width="10%">Appointments_Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doctors as $i => $doctor)
            @php
                $appointmentCount = $doctor->appointments_count ?? 0;
                $rating = $doctor->reviews_avg_rating ?? 0;
                $performance = $appointmentCount > 0 ? min(100, ($appointmentCount / 10) * 100) : 0;
                $performanceClass = $appointmentCount >= 15 ? 'high' : ($appointmentCount >= 8 ? 'medium' : 'low');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>Dr. {{ $doctor->first_name }}</strong></td>
                <td>{{ $doctor->specialization->name ?? 'N/A' }}</td>
                <td>{{ $appointmentCount }}</td>
                <td>
                    <div class="performance-bar">
                        <div class="performance-fill" style="width: {{ $performance }}%"></div>
                    </div>
                </td>
                <td>
                    <span class="rating-stars">
                        {{ number_format($rating, 1) }} ⭐
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $performanceClass }}">
                        {{ ucfirst($performanceClass) }}
                    </span>
                </td>
                <td>{{ $doctor->appointments->first()->status ?? 0 }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>HealthCare - Performance Management System | Confidential Report</p>
        <p>Performance metrics based on completed appointments and patient reviews</p>
    </div>
</body>
</html>