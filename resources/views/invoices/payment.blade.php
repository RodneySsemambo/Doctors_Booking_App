<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $payment->id }} - HealthCare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1F2937;
            line-height: 1.6;
        }
        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Header Section */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3B82F6;
        }
        .company-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .company-info h1 {
            color: #3B82F6;
            font-size: 32px;
            margin-bottom: 5px;
        }
        .company-info .tagline {
            color: #6B7280;
            font-size: 12px;
        }
        .invoice-meta {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .invoice-number {
            font-size: 24px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 10px;
        }
        .invoice-date {
            color: #6B7280;
            font-size: 11px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
        }
        .status-completed { background: #D1FAE5; color: #065F46; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-failed { background: #FEE2E2; color: #991B1B; }

        /* Bill To / Doctor Info */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .bill-to, .doctor-info {
            display: table-cell;
            width: 50%;
            padding: 20px;
            vertical-align: top;
        }
        .bill-to {
            background: #F9FAFB;
            border-left: 4px solid #3B82F6;
        }
        .doctor-info {
            background: #F3F4F6;
            border-left: 4px solid #10B981;
        }
        .info-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #6B7280;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .info-content {
            font-size: 11px;
            line-height: 1.8;
        }
        .info-content strong {
            color: #1F2937;
        }

        /* Appointment Details */
        .appointment-details {
            background: #EFF6FF;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .appointment-details table {
            width: 100%;
        }
        .appointment-details td {
            padding: 5px 0;
            font-size: 11px;
        }
        .appointment-details strong {
            color: #1E40AF;
        }

        /* Invoice Items Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table thead {
            background: #1F2937;
            color: white;
        }
        .invoice-table th {
            padding: 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .invoice-table tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }
        .invoice-table td {
            padding: 15px 12px;
        }
        .invoice-table .description {
            color: #1F2937;
            font-weight: 500;
        }
        .invoice-table .amount {
            text-align: right;
            font-weight: 600;
            color: #3B82F6;
        }

        /* Totals Section */
        .totals-section {
            width: 350px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .totals-row {
            display: table;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        .totals-row.final {
            background: #1F2937;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            border-bottom: none;
        }
        .totals-label {
            display: table-cell;
            text-align: left;
        }
        .totals-value {
            display: table-cell;
            text-align: right;
            font-weight: 600;
        }

        /* Payment Details */
        .payment-details {
            background: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-details h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #1F2937;
            margin-bottom: 12px;
        }
        .payment-method {
            display: inline-block;
            padding: 8px 16px;
            background: #DBEAFE;
            color: #1E40AF;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
            margin-right: 15px;
        }
        .transaction-id {
            color: #6B7280;
            font-size: 10px;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
        }
        .thank-you {
            font-size: 16px;
            color: #3B82F6;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .footer-info {
            color: #6B7280;
            font-size: 10px;
            line-height: 1.8;
        }
        .footer-contact {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
        }

        /* Notes Section */
        .notes-section {
            background: #FFFBEB;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin-bottom: 30px;
        }
        .notes-section h4 {
            font-size: 11px;
            color: #92400E;
            margin-bottom: 8px;
        }
        .notes-section p {
            font-size: 10px;
            color: #78350F;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>HealthCare</h1>
                <p class="tagline">Professional Medical Services</p>
            </div>
            <div class="invoice-meta">
                <div class="invoice-number">INVOICE #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">
                    Date: {{ \Carbon\Carbon::parse($payment->initiated_at)->format('M d, Y') }}
                </div>
                <span class="status-badge status-{{ $payment->status }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>

        <!-- Bill To & Doctor Info -->
        <div class="info-section">
            <div class="bill-to">
                <div class="info-title">Bill To</div>
                <div class="info-content">
                    <strong>{{ $payment->appointment->patient->first_name }} {{ $payment->appointment->patient->last_name ?? '' }}</strong><br>
                    {{ $payment->appointment->patient->user->email }}<br>
                    {{ $payment->appointment->patient->user->phone }}<br>
                    @if($payment->appointment->patient->address)
                        {{ $payment->appointment->patient->address }}
                    @endif
                </div>
            </div>
            <div class="doctor-info">
                <div class="info-title">Consulting Doctor</div>
                <div class="info-content">
                    <strong>Dr. {{ $payment->appointment->doctor->first_name }} {{ $payment->appointment->doctor->last_name ?? '' }}</strong><br>
                    {{ $payment->appointment->doctor->specialization->name ?? 'General Practice' }}<br>
                    License: {{ $payment->appointment->doctor->license_number ?? 'N/A' }}<br>
                    {{ $payment->appointment->doctor->hospitals ?? 'N/A' }}<br>
                </div>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="appointment-details">
            <table>
                <tr>
                    <td width="50%"><strong>Appointment Date:</strong> {{ \Carbon\Carbon::parse($payment->appointment->appointment_date)->format('l, M d, Y') }}</td>
                    <td width="50%"><strong>Appointment Time:</strong> {{ \Carbon\Carbon::parse($payment->appointment->appointment_time)->format('h:i A') }}</td>
                </tr>
                <tr>
                    <td><strong>Appointment Type:</strong> {{ ucfirst(str_replace('-', ' ', $payment->appointment->appointment_type)) }}</td>
                    <td><strong>Appointment ID:</strong> #{{ str_pad($payment->appointment->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
            </table>
        </div>

        <!-- Invoice Items -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="60%">Description</th>
                    <th width="20%">Quantity</th>
                    <th width="20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="description">
                        Medical Consultation Fee<br>
                        <span style="font-size: 10px; color: #6B7280;">
                            {{ $payment->appointment->doctor->specialization->name ?? 'General Consultation' }}
                        </span>
                    </td>
                    <td>1</td>
                    <td class="amount">UGX {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">UGX {{ number_format($payment->amount, 2) }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">Tax (0%):</div>
                <div class="totals-value">UGX 0.00</div>
            </div>
            <div class="totals-row final">
                <div class="totals-label">Total Amount:</div>
                <div class="totals-value">UGX {{ number_format($payment->amount, 2) }}</div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <h3>Payment Method</h3>
            <span class="payment-method">
                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
            </span>
            @if($payment->transaction_id)
                <span class="transaction-id">
                    Transaction ID: {{ $payment->transaction_id }}
                </span>
            @endif
        </div>

        <!-- Notes -->
        @if($payment->notes)
        <div class="notes-section">
            <h4>Additional Notes</h4>
            <p>{{ $payment->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="thank-you">Thank You for Choosing HealthCare!</div>
            <div class="footer-info">
                This is a computer-generated invoice and is valid without signature.<br>
                All payments are non-refundable unless otherwise stated in our terms of service.
            </div>
            <div class="footer-contact">
                <strong>Contact Us:</strong> info@healthcare.com | +256 7413964<br>
                <strong>Address:</strong> Kampala, Uganda | www.healthcare.com
            </div>
        </div>
    </div>
</body>
</html>