<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'specialization_id' => 'required|exists:specializations,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:doctors,license_number|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'qualification' => 'required|string',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'consultation_fee' => 'required|numeric|min:0',
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
            'user_id.required' => 'userID is required',
            'user_id.exists' => 'user does not exist',
            'specialization_id.required' => 'Specialization is required',
            'specialization_id.exists' => 'Specialization does not exist',
            'license_number.unique' => 'This license number is already registered',
            'profile_photo.image' => 'Profile photo must be an image',
            'profile_photo.max' => 'Profile photo must not exceed 2MB',
        ];
    }
}
