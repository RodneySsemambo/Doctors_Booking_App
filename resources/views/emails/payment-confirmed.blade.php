<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .success-badge { background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .amount { font-size: 32px; font-weight: bold; color: #10B981; text-align: center; margin: 20px 0; }
        .button { display: inline-block; background: #10B981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; color: #6B7280; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💳 Payment Successful</h1>
        </div>
        
        <div class="content">
            <div class="success-badge">
                <strong>✓ Your payment has been received successfully!</strong>
            </div>
            
            <p>Dear {{ $payment->appointment->patient->first_name }},</p>
            
            <p>Thank you for your payment. Your transaction has been processed successfully.</p>
            
            <div class="amount">
                UGX {{ number_format($payment->amount) }}
            </div>
            
            <div class="details">
                <h3>Payment Details</h3>
                
                <div class="detail-row">
                    <strong>Payment Number:</strong> {{ $payment->payment_number }}
                </div>
                
                <div class="detail-row">
                    <strong>Transaction Reference:</strong> {{ $payment->transaction_reference }}
                </div>
                
                <div class="detail-row">
                    <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                </div>
                
                <div class="detail-row">
                    <strong>Date:</strong> {{ $payment->completed_at ? \Carbon\Carbon::parse($payment->completed_at)->format('M d, Y h:i A') : 'N/A' }}
                </div>
                
                <div class="detail-row">
                    <strong>Appointment:</strong> #{{ $payment->appointment_id }}
                </div>
            </div>
            
            <center>
                <a href="{{ config('app.url') }}/patient/payments/{{ $payment->id }}/receipt" class="button">Download Receipt</a>
            </center>
        </div>
        
        <div class="footer">
            <p>HealthCare Medical Services</p>
            <p>Email: info@healthcare.com | Phone: +256 XXX XXX XXX</p>
            <p>&copy; {{ date('Y') }} HealthCare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>