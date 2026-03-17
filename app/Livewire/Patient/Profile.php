<?php

namespace App\Livewire\Patient;

use App\Services\PatientService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public $patient;
    public $first_name;
    public $last_name;
    public $phone_number;
    public $date_of_birth;
    public $gender;
    public $city;
    public $address;
    public $state;
    public $blood_group;
    public $profile_photo;
    public $newProfilePhoto;

    protected $patientService;

    public function boot(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function mount()
    {
        $this->patient = auth()->user()->patient;
        $this->first_name = $this->patient->first_name;
        $this->last_name = $this->patient->last_name;
        $this->phone_number = $this->patient->phone_number;
        $this->date_of_birth = $this->patient->date_of_birth;
        $this->gender = $this->patient->gender;
        $this->city = $this->patient->city;
        $this->address = $this->patient->address;
        $this->state = $this->patient->state;
        $this->blood_group = $this->patient->blood_group;
        $this->profile_photo = $this->patient->profile_photo;
    }

    public function updateProfile()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'newProfilePhoto' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'city' => $this->city,
                'address' => $this->address,
                'state' => $this->state,
                'blood_group' => $this->blood_group,
            ];

            if ($this->newProfilePhoto) {
                $data['profile_photo'] = $this->newProfilePhoto;
            }

            $this->patientService->updatePatient($this->patient->id, $data);

            session()->flash('success', 'Profile updated successfully!');
            $this->patient = auth()->user()->patient->fresh();
            $this->newProfilePhoto = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.patient.profile')
            ->layout('layouts.patient', ['title' => 'My Profile']);
    }
}
