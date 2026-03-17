<?php

use App\Livewire\Patient\Dashboard as PatientDashboard;
use App\Livewire\Doctor\Dashboard as DoctorDashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Patient\Appointments\AppointmentDetails;
use App\Livewire\Patient\Appointments\BookAppointment;
use App\Livewire\Patient\Appointments\MyAppointments;
use App\Livewire\Patient\MedicalInfo;
use App\Livewire\Patient\Payments\PaymentHistory;
use App\Livewire\Patient\Prescriptions\MyPrescriptions;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PaymentController;
use App\Livewire\Patient\Profile;
use App\Jobs\SendPaymentNotificationJob;
use App\Livewire\Doctor\Appointments;
use App\Livewire\Doctor\Patients;
use App\Livewire\Doctor\Payment as DoctorPayment;
use App\Livewire\Doctor\Payments;
use App\Livewire\Doctor\Settings;
use App\Livewire\Doctor\Timeslot;
use App\Livewire\Doctor\Withdrawals;
use App\Livewire\Patient\MedicalHistory;


use App\Models\Payment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email-fix', function () {
    // Force clear config
    Config::set('mail.mailers.smtp.encryption', 'tls');

    $config = [
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
        'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '***HIDDEN***' : 'NOT SET',
    ];

    dump('Mail Configuration:', $config);

    try {
        Mail::raw('✅ Email test successful! Your SMTP is working.', function ($message) {
            $message->to('ssemamborodney94@gmail.com')
                ->subject('Test Email - HealthCare - ' . now());
        });

        return '✅ SUCCESS! Email sent. Check your inbox.';
    } catch (\Exception $e) {
        return '❌ ERROR: ' . $e->getMessage();
    }
});


Route::get('/test-queue', function () {
    $payment = Payment::first();

    if ($payment) {
        SendPaymentNotificationJob::dispatch($payment);
        return 'Job dispatched! Check your queue worker terminal.';
    }

    return 'No payment found to test.';
});

Route::get('/test-email-detailed', function () {
    $config = [
        'MAIL_MAILER' => config('mail.mailers.smtp.transport'),
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
        'MAIL_FROM_ADDRESS' => config('mail.from.address'),
        'MAIL_FROM_NAME' => config('mail.from.name'),
    ];

    dump('Current Mail Configuration:', $config);

    try {
        Mail::raw('Test email from HealthCare. If you receive this, email is working!', function ($message) {
            $message->to(config('mail.mailers.smtp.username'))
                ->subject('HealthCare Email Test - ' . now());
        });

        return '✅ Email sent! Check your inbox at: ' . config('mail.mailers.smtp.username');
    } catch (\Exception $e) {
        return '❌ Failed: ' . $e->getMessage() . '<br><br>Config: ' . json_encode($config, JSON_PRETTY_PRINT);
    }
});

//payment callback route
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Patient Routes
Route::middleware(['auth', 'patient.access'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', PatientDashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/appointments', MyAppointments::class)->name('appointments');
    Route::get('/book-appointment', BookAppointment::class)->name('book-appointment');
    Route::get('/my-appointments', MyAppointments::class)->name('my-appointments');
    Route::get('/appointment-details/{appointment_id}', AppointmentDetails::class)->name('appointment-details');
    Route::get('/medical-history', MedicalHistory::class)->name('medical-history');
    Route::get('/medical-history/{id}/download', MedicalHistory::class, 'download')
        ->name('patient.medical-history.download');

    Route::get('/medical-history/stats', MedicalHistory::class, 'stats');
    Route::get('/medical-history/timeline', MedicalHistory::class, 'timeline');
    Route::post('/medical-history/search', MedicalHistory::class, 'search');
    Route::post('/medical-history/upload', MedicalHistory::class, 'upload');
    Route::get('/prescriptions', MyPrescriptions::class)->name('prescriptions');
    Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show'])
        ->name('patient.prescriptions.show');
    Route::get('/prescriptions/{id}/print', [PrescriptionController::class, 'print'])
        ->name('patient.prescriptions.print');
    Route::get('/prescriptions/{id}/download', [PrescriptionController::class, 'download'])
        ->name('patient.prescriptions.download');
    Route::get('/payments', PaymentHistory::class)->name('payments');
    Route::get('/medical-records', MedicalInfo::class)->name('medical-records');
});


//doctor routes
Route::middleware(['auth', 'doctor.access'])->prefix('doctor')->name('doctor.')->group(function () {

    Route::get('/dashboard', DoctorDashboard::class)->name('dashboard');
    Route::get('/appointments', Appointments::class)->name('appointments');
    Route::get('/patients', Patients::class)->name('patients');
    Route::get('/payments', Payments::class)->name('payments');
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/timeslots', Timeslot::class)->name('timeslots');
    Route::get('/withdrawal', Withdrawals::class)->name('withdrawal');
});
