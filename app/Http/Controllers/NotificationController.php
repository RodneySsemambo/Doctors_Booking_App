<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct($notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function confirmAppointment($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor'])
            ->findOrFail($appointmentId);

        $this->notificationService->sendAppointmentConfirmation($appointment);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment confirmation sent successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Send appointment cancellation
     */
    public function cancelAppointment($appointmentId)
    {
        $appointment = Appointment::with(['patient.user'])
            ->findOrFail($appointmentId);

        $this->notificationService->sendAppointmentCancellation($appointment);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment cancellation sent successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Send payment confirmation
     */
    public function confirmPayment($paymentId)
    {
        $payment = Payment::with(['appointment.patient.user'])
            ->findOrFail($paymentId);

        $this->notificationService->sendPaymentConfirmation($payment);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment confirmation sent successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Send refund notification
     */
    public function refundPayment($paymentId)
    {
        $payment = Payment::with(['patient.user'])
            ->findOrFail($paymentId);

        $this->notificationService->sendRefundNotification($payment);

        return response()->json([
            'status' => 'success',
            'message' => 'Refund notification sent successfully'
        ], Response::HTTP_OK);
    }

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest('sent_at')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications
     */
    public function unread()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->latest('sent_at')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $notifications->count(),
            'data' => $notifications
        ]);
    }

    /**
     * Get unread count only
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $updated = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'count' => $updated
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        $deleted = Notification::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'All read notifications deleted',
            'count' => $deleted
        ]);
    }
}
