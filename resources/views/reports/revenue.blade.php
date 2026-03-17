<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report - HealthCare</title>
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
            border-bottom: 3px solid #10B981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #10B981;
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
        .revenue-summary {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .revenue-summary .amount {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .revenue-summary .label {
            font-size: 12px;
            opacity: 0.9;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            background: #F9FAFB;
            border-right: 1px solid #E5E7EB;
        }
        .stat-card:last-child {
            border-right: none;
        }
        .stat-card .number {
            font-size: 20px;
            font-weight: bold;
            color: #10B981;
            margin-bottom: 5px;
        }
        .stat-card .label {
            color: #6B7280;
            font-size: 10px;
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
        .amount-cell {
            font-weight: 600;
            color: #10B981;
        }
        .method-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            background: #DBEAFE;
            color: #1E40AF;
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
        <div class="subtitle">Financial Revenue Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <table>
            <tr>
                <td width="50%"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($date_to)->format('M d, Y') }}</td>
                <td width="50%"><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Total Transactions:</strong> {{ $total_transactions }}</td>
                <td><strong>Report Type:</strong> Revenue Analysis</td>
            </tr>
        </table>
    </div>

    <!-- Revenue Summary -->
    <div class="revenue-summary">
        <div class="label">Total Revenue Collected</div>
        <div class="amount">UGX {{ number_format($total_revenue, 2) }}</div>
        <div class="label">{{ $total_transactions }} Completed Transactions</div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="stats-grid">
        @php
            $methods = ['cash' => 'Cash', 'card' => 'Card', 'mobile_money' => 'Mobile Money'];
            $methodData = $by_method ?? collect();
        @endphp
        @foreach($methods as $key => $label)
        <div class="stat-card">
            <div class="number">UGX {{ number_format($methodData->get($key, 0)) }}</div>
            <div class="label">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <!-- Transactions Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="20%">Patient</th>
                <th width="20%">Doctor</th>
                <th width="15%">Amount</th>
                <th width="15%">Method</th>
                <th width="15%">Status</th>
                <th width="10%">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payment->appointment->patient->first_name ?? 'N/A' }}</td>
                <td>Dr. {{ $payment->appointment->doctor->first_name ?? 'N/A' }}</td>
                <td class="amount-cell">UGX {{ number_format($payment->amount) }}</td>
                <td>
                    <span class="method-badge">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                    </span>
                </td>
                <td style="font-size: 9px;">{{ ucfirst($payment->status) }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->initiated_at)->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>HealthCare - Financial Management System | Confidential Report</p>
        <p>This report contains sensitive financial information</p>
    </div>
</body>
</html>