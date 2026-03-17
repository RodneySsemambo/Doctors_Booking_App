<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type',
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'last_login_at',
        'phone_verified_at',
        'email_notifications',
        'sms_notifications',
        'appointment_reminders',
        'payment_notifications',
        'new_patient_notifications',
    ];


    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'appointment_reminders' => 'boolean',
        'payment_notifications' => 'boolean',
        'new_patient_notifications' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function canAccessPanel(Panel $panel): bool
    {
        // First check: Can they access the admin panel at all?
        if ($panel->getId() === 'admin') {
            // Allow access only if they have the specific permission
            // AND have at least one role
            return  $this->roles()->count() > 0;
        }

        // For other panels (doctor, patient)
        if ($panel->getId() === 'doctor') {
            return $this->user_type === 'doctor';
        }

        if ($panel->getId() === 'patient') {
            return $this->user_type === 'patient';
        }

        return false;
    }

    /**
     * Check if user can view a specific page
     */
    public function canViewPage(string $pageClass): bool
    {
        $permissionName = 'view_page_' . class_basename($pageClass);
        return $this->can($permissionName);
    }

    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function chatbot_Conversation(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    public function notification(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
