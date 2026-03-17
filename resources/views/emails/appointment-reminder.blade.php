<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #FFFBEB; padding: 30px; border: 1px solid #FCD34D; }
        .reminder-box { background: white; border-left: 4px solid #F59E0B; padding: 20px; margin: 20px 0; }
        .time-box { background: #FEF3C7; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .time-box .time { font-size: 24px; font-weight: bold; color: #92400E; }
        .button { display: inline-block; background: #F59E0B; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; color: #6B7280; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Appointment Reminder</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $appointment->patient->first_name }},</p>
            
            <div class="reminder-box">
                <h2 style="color: #92400E; margin-top: 0;">Your appointment is tomorrow!</h2>
                <p>This is a friendly reminder about your upcoming appointment.</p>
            </div>
            
            <div class="time-box">
                <div>Tomorrow</div>
                <div class="time">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
            </div>
            
            <p><strong>Appointment Details:</strong></p>
            <ul>
                <li><strong>Doctor:</strong> Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</li>
                <li><strong>Location:</strong> {{ $appointment->doctor->hospital->name ?? 'N/A' }}</li>
                <li><strong>Address:</strong> {{ $appointment->doctor->hospital->address ?? 'N/A' }}</li>
            </ul>
            
            <p><strong>Please remember to:</strong></p>
            <ul>
                <li>Arrive 10 minutes early</li>
                <li>Bring your ID and insurance card</li>
                <li>Bring any relevant medical documents</li>
            </ul>
            
            <center>
                <a href="{{ config('app.url') }}/patient/appointments/{{ $appointment->id }}" class="button">View Details</a>
            </center>
            
            <p style="margin-top: 30px; font-size: 12px; color: #6B7280;">
                Need to reschedule? Contact us at info@healthcare.com or +256 XXX XXX XXX
            </p>
        </div>
        
        <div class="footer">
            <p>HealthCare Medical Services</p>
            <p>&copy; {{ date('Y') }} HealthCare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>