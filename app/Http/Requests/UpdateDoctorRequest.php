<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $doctor_id = $this->route('doctor') ?? $this->route('id');
        return [
            'user_id' => 'sometimes|exists:users,id',
            'specialization' => 'sometimes|exists:specializations,id',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'license_number' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('doctors', 'license_number')->ignore($doctor_id)
            ],
            'years_of_experience' => 'sometimes|integer|min:0',
            'qualification' => 'sometimes|string',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'consultation_fee' => 'sometimes|numeric|min:0',
            'hospital_affiliation' => 'nullable|string',
            'video_consultation_available' => 'boolean',
            'languages_spoken' => 'nullable|array',
            'languages_spoken.*' => 'string',
            'is_verified' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function messages()
    {
        return [

            'user_id.exists' => 'User does not exist',
            'specialization_id.exists' => 'Specialization does not exist',
            'license_number.unique' => 'This license number is already registered',
            'profile_photo.image' => 'Profile photo must be an image',
            'profile_photo.max' => 'Profile photo must not exceed 2MB',
        ];
    }
}
