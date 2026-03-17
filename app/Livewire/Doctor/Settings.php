<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Specialization;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    public $doctor;

    // Profile Settings
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $specialization_id;
    public $qualifications = '';
    public $experience_years = 0;
    public $bio = '';
    public $consultation_fee = 0;
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $postal_code = '';
    public $profile_photo;
    public $profilePhotoPreview;

    // Account Settings
    public $current_password;
    public $new_password;
    public $confirm_password;

    // Notification Settings
    public $email_notifications = true;
    public $sms_notifications = true;
    public $appointment_reminders = true;
    public $payment_notifications = true;
    public $new_patient_notifications = true;

    // Working Hours
    public $working_hours = [];
    public $specializations = [];

    // Security
    public $two_factor_enabled = false;

    // Loading states
    public $savingProfile = false;
    public $savingPassword = false;
    public $savingNotifications = false;
    public $savingHours = false;
    public $togglingTwoFactor = false;
    public $exportingData = false;

    // Success messages
    public $profileSuccess = '';
    public $passwordSuccess = '';
    public $notificationsSuccess = '';
    public $hoursSuccess = '';
    public $securitySuccess = '';
    public $exportSuccess = '';
    public $deleteWarning = '';

    public function mount()
    {
        $this->doctor = auth()->user()->doctor->load('user');
        $this->loadDoctorData();
        $this->loadWorkingHours();
        $this->specializations = Specialization::all();

        // Default working hours
        if (empty($this->working_hours)) {
            $this->working_hours = [
                'monday' => ['start' => '09:00', 'end' => '17:00', 'enabled' => true],
                'tuesday' => ['start' => '09:00', 'end' => '17:00', 'enabled' => true],
                'wednesday' => ['start' => '09:00', 'end' => '17:00', 'enabled' => true],
                'thursday' => ['start' => '09:00', 'end' => '17:00', 'enabled' => true],
                'friday' => ['start' => '09:00', 'end' => '17:00', 'enabled' => true],
                'saturday' => ['start' => '10:00', 'end' => '14:00', 'enabled' => false],
                'sunday' => ['start' => '10:00', 'end' => '14:00', 'enabled' => false],
            ];
        }
    }

    public function loadDoctorData()
    {
        $user = $this->doctor->user;

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->specialization_id = $this->doctor->specialization_id;
        $this->qualifications = $this->doctor->qualifications ?? '';
        $this->experience_years = $this->doctor->experience_years ?? 0;
        $this->bio = $this->doctor->bio ?? '';
        $this->consultation_fee = $this->doctor->consultation_fee ?? 0;
        $this->address = $this->doctor->address ?? '';
        $this->city = $this->doctor->city ?? '';
        $this->state = $this->doctor->state ?? '';
        $this->country = $this->doctor->country ?? '';
        $this->postal_code = $this->doctor->postal_code ?? '';

        // Notification settings (you might want to store these in database)
        // For now, we'll set defaults
        $this->email_notifications = $user->email_notifications ?? true;
        $this->sms_notifications = $user->sms_notifications ?? true;
        $this->appointment_reminders = $user->appointment_reminders ?? true;
        $this->payment_notifications = $user->payment_notifications ?? true;
        $this->new_patient_notifications = $user->new_patient_notifications ?? true;
    }

    public function loadWorkingHours()
    {
        if ($this->doctor->working_hours) {
            $this->working_hours = json_decode($this->doctor->working_hours, true);
        }
    }

    public function updatedProfilePhoto()
    {
        $this->validate([
            'profile_photo' => 'image|max:2048', // 2MB Max
        ]);

        $this->profilePhotoPreview = $this->profile_photo->temporaryUrl();
    }

    public function saveProfile()
    {
        $this->savingProfile = true;
        $this->profileSuccess = '';

        try {
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->doctor->user_id,
                'phone' => 'nullable|string|max:20',
                'specialization_id' => 'required|exists:specializations,id',
                'qualifications' => 'nullable|string|max:1000',
                'experience_years' => 'nullable|integer|min:0|max:60',
                'bio' => 'nullable|string|max:2000',
                'consultation_fee' => 'nullable|numeric|min:0',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'profile_photo' => 'nullable|image|max:2048',
            ]);

            // Update user data
            $user = $this->doctor->user;
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            // Update doctor data
            $doctorData = [
                'specialization_id' => $this->specialization_id,
                'qualifications' => $this->qualifications,
                'experience_years' => $this->experience_years,
                'bio' => $this->bio,
                'consultation_fee' => $this->consultation_fee,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
            ];

            // Handle profile photo upload
            if ($this->profile_photo) {
                $path = $this->profile_photo->store('profile-photos', 'public');
                $doctorData['profile_photo'] = $path;

                // Delete old profile photo if exists
                if ($this->doctor->profile_photo) {
                    Storage::disk('public')->delete($this->doctor->profile_photo);
                }
            }

            $this->doctor->update($doctorData);

            $this->profileSuccess = 'Profile updated successfully!';
        } catch (\Exception $e) {
            $this->addError('profile', 'Error updating profile: ' . $e->getMessage());
        } finally {
            $this->savingProfile = false;
        }
    }

    public function savePassword()
    {
        $this->savingPassword = true;
        $this->passwordSuccess = '';

        try {
            $this->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|different:current_password',
                'confirm_password' => 'required|same:new_password',
            ]);

            $user = $this->doctor->user;

            // Check current password
            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'The current password is incorrect.');
                return;
            }

            // Update password
            $user->update([
                'password' => Hash::make($this->new_password)
            ]);

            // Clear password fields
            $this->reset(['current_password', 'new_password', 'confirm_password']);

            $this->passwordSuccess = 'Password updated successfully!';
        } catch (\Exception $e) {
            $this->addError('password', 'Error updating password: ' . $e->getMessage());
        } finally {
            $this->savingPassword = false;
        }
    }

    public function saveNotifications()
    {
        $this->savingNotifications = true;
        $this->notificationsSuccess = '';

        try {
            $this->validate([
                'email_notifications' => 'boolean',
                'sms_notifications' => 'boolean',
                'appointment_reminders' => 'boolean',
                'payment_notifications' => 'boolean',
                'new_patient_notifications' => 'boolean',
            ]);

            $user = $this->doctor->user;

            $user->update([
                'email_notifications' => $this->email_notifications,
                'sms_notifications' => $this->sms_notifications,
                'appointment_reminders' => $this->appointment_reminders,
                'payment_notifications' => $this->payment_notifications,
                'new_patient_notifications' => $this->new_patient_notifications,
            ]);

            $this->notificationsSuccess = 'Notification preferences updated successfully!';
        } catch (\Exception $e) {
            $this->addError('notifications', 'Error updating notification preferences: ' . $e->getMessage());
        } finally {
            $this->savingNotifications = false;
        }
    }

    public function saveWorkingHours()
    {
        $this->savingHours = true;
        $this->hoursSuccess = '';

        try {
            $this->validate([
                'working_hours.monday.start' => 'required|date_format:H:i',
                'working_hours.monday.end' => 'required|date_format:H:i|after:working_hours.monday.start',
                'working_hours.tuesday.start' => 'required|date_format:H:i',
                'working_hours.tuesday.end' => 'required|date_format:H:i|after:working_hours.tuesday.start',
                'working_hours.wednesday.start' => 'required|date_format:H:i',
                'working_hours.wednesday.end' => 'required|date_format:H:i|after:working_hours.wednesday.start',
                'working_hours.thursday.start' => 'required|date_format:H:i',
                'working_hours.thursday.end' => 'required|date_format:H:i|after:working_hours.thursday.start',
                'working_hours.friday.start' => 'required|date_format:H:i',
                'working_hours.friday.end' => 'required|date_format:H:i|after:working_hours.friday.start',
                'working_hours.saturday.start' => 'nullable|date_format:H:i',
                'working_hours.saturday.end' => 'nullable|date_format:H:i|after:working_hours.saturday.start',
                'working_hours.sunday.start' => 'nullable|date_format:H:i',
                'working_hours.sunday.end' => 'nullable|date_format:H:i|after:working_hours.sunday.start',
            ]);

            $this->doctor->update([
                'working_hours' => json_encode($this->working_hours)
            ]);

            $this->hoursSuccess = 'Working hours updated successfully!';
        } catch (\Exception $e) {
            $this->addError('hours', 'Error updating working hours: ' . $e->getMessage());
        } finally {
            $this->savingHours = false;
        }
    }

    public function toggleTwoFactor()
    {
        $this->togglingTwoFactor = true;
        $this->securitySuccess = '';

        try {
            // Simulate API call
            sleep(1); // Remove this in production

            $this->two_factor_enabled = !$this->two_factor_enabled;
            // In a real app, you would implement actual 2FA setup here

            $this->securitySuccess = 'Two-factor authentication ' . ($this->two_factor_enabled ? 'enabled' : 'disabled') . ' successfully!';
        } catch (\Exception $e) {
            $this->addError('security', 'Error toggling two-factor authentication: ' . $e->getMessage());
        } finally {
            $this->togglingTwoFactor = false;
        }
    }

    public function exportData()
    {
        $this->exportingData = true;
        $this->exportSuccess = '';

        try {
            // Simulate export process
            sleep(2); // Remove this in production

            // In a real app, you would generate and download a data export
            $this->exportSuccess = 'Data export request submitted. You will receive an email with download link.';
        } catch (\Exception $e) {
            $this->addError('export', 'Error exporting data: ' . $e->getMessage());
        } finally {
            $this->exportingData = false;
        }
    }

    public function deleteAccount()
    {
        $this->deleteWarning = 'Account deletion requires additional confirmation. Please contact support at support@healthcare.com';
    }

    public function clearMessage($type)
    {
        switch ($type) {
            case 'profile':
                $this->profileSuccess = '';
                break;
            case 'password':
                $this->passwordSuccess = '';
                break;
            case 'notifications':
                $this->notificationsSuccess = '';
                break;
            case 'hours':
                $this->hoursSuccess = '';
                break;
            case 'security':
                $this->securitySuccess = '';
                break;
            case 'export':
                $this->exportSuccess = '';
                break;
            case 'delete':
                $this->deleteWarning = '';
                break;
        }
    }

    public function render()
    {
        return view('livewire.doctor.setting')
            ->layout('layouts.doctor', [
                'title' => 'Settings',
                'todayAppointmentsCount' => $this->doctor->appointments()
                    ->whereDate('appointment_date', today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
                'monthlyEarnings' => Payment::whereHas('appointment', function ($q) {
                    $q->where('doctor_id', $this->doctor->id);
                })
                    ->where('status', 'completed')
                    ->whereMonth('completed_at', now()->month)
                    ->sum('amount'),
                'recentNotifications' => Notification::where('user_id', auth()->id())
                    ->latest('sent_at')
                    ->limit(5)
                    ->get(),
                'unreadNotificationsCount' => Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count()
            ]);
    }
}
