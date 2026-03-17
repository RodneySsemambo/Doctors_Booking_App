<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: table; width: 100%; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-label { font-weight: bold; color: #6B7280; }
        .detail-value { color: #1F2937; }
        .button { display: inline-block; background: #3B82F6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6B7280; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Appointment Confirmed</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }},</p>
            
            <p>Your appointment has been successfully confirmed!</p>
            
            <div class="details">
                <h3>Appointment Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Doctor:</div>
                    <div class="detail-value">Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Specialization:</div>
                    <div class="detail-value">{{ $appointment->doctor->specialization->name ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value">{{ $appointment->doctor->hospital->name ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Consultation Fee:</div>
                    <div class="detail-value">UGX {{ number_format($appointment->consultation_fee) }}</div>
                </div>
            </div>
            
            <p><strong>Important Reminders:</strong></p>
            <ul>
                <li>Please arrive 10 minutes before your appointment time</li>
                <li>Bring your ID and any relevant medical documents</li>
                <li>Contact us if you need to reschedule</li>
            </ul>
            
            <center>
                <a href="{{ config('app.url') }}/patient/appointments" class="button">View Appointment</a>
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