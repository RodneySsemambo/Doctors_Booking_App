<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use League\Csv\Query\Row;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Doctor routes
Route::prefix('doctors')->group(function () {
    //public routes
    Route::get('/', [DoctorController::class, 'index']);
    Route::get('/top-rated', [DoctorController::class, 'topRatedDoctors']);
    Route::get('/specialization/{specialization_id}', [DoctorController::class, 'doctorAvailabilityBySpecialization']);
    Route::get('/{doctor_id}', [DoctorController::class, 'show']);
    //protected routes(require authentication)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [DoctorController::class, 'store']);
        Route::put('/{doctor_id}', [DoctorController::class, 'update']);
        Route::delete('/{doctor_id}', [DoctorController::class, 'destroy']);
        Route::post('/{doctor_id}/toggle-availability', [DoctorController::class, 'toggleDoctorAvailability']);
        Route::post('/{doctor_id}/verify', [DoctorController::class, 'doctorVerification']);
    });
});

//patient routes
Route::middleware(['auth:sanctum', 'patient.access'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/profile', [PatientController::class, 'getProfile']);
    Route::post('/profile', [PatientController::class, 'updateProfile']);
    Route::get('/dashboard/stats', [PatientController::class, 'getDashboardStats']);
    Route::get('/dashboard/activity', [PatientController::class, 'getRecentActivity']);
    Route::get('/appointments', [PatientController::class, 'getAppointments']);
    Route::get('/appointments/upcoming', [PatientController::class, 'getUpcomingAppointments']);
    Route::get('/appointments/search', [PatientController::class, 'searchAppointments']);
    Route::get('/medical-history', [PatientController::class, 'getMedicalHistory']);
    Route::post('/medical-info', [PatientController::class, 'updateMedicalInfo']);
    Route::get('/prescriptions', [PatientController::class, 'getPrescriptions']);
    Route::get('/payments', [PatientController::class, 'getPayments']);
});

//payment routes    
Route::prefix('payments')->group(function () {
    // Webhook callback (MarzPay will call this)
    Route::post('marzpay/callback', [PaymentController::class, 'handleMarzPayCallback'])
        ->name('api.payments.marzpay.callback');

    // Verify payment
    Route::get('verify/{paymentId}', [PaymentController::class, 'verifyPayment'])
        ->name('api.payments.verify');

    // Test connection
    Route::get('test-connection', [PaymentController::class, 'testConnection'])
        ->name('api.payments.test');

    // Get payment details
    Route::get('{paymentId}', [PaymentController::class, 'getPayment'])
        ->name('api.payments.show');
});


//chatbot routes
Route::prefix('chatbot')->middleware('auth:sanctum')->group(function () {
    Route::post('/conversation/start', [ChatController::class, 'startConversation']);
    Route::get('/conversation', [ChatController::class, 'getOrCreateConversation']);
    Route::post('/conversation/{id}/close', [ChatController::class, 'closeConversation']);
    Route::post('/message', [ChatController::class, 'sendMessage']);
    Route::get('/conversation/{id}/history', [ChatController::class, 'getHistory']);
    Route::get('/user/{userId}/conversations', [ChatController::class, 'getUserConversations']);
    Route::get('/stats', [ChatController::class, 'getStats'])
        ->middleware('admin.access');
});

//notification routes
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {

    Route::post('/appointment/{id}/confirm', [NotificationController::class, 'confirmAppointment']);
    Route::post('/appointment/{id}/cancel', [NotificationController::class, 'cancelAppointment']);

    Route::post('/payment/{id}/confirm', [NotificationController::class, 'confirmPayment']);
    Route::post('/payment/{id}/refund', [NotificationController::class, 'refundPayment']);

    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});
