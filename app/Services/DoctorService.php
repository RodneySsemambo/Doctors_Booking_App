<?php

namespace App\Services;

use App\Models\Doctor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage as Storage;
use PhpParser\Comment\Doc;

class DoctorService
{

    //search doctor with filters

    public function searchDoctors($filters)
    {

        $query = Doctor::with(['specialization', 'hospital'])
            ->where('is_verified', true)
            ->where('is_available', true);

        //filter by specialization
        if (!empty($filters['specialization_id'])) {
            $query->where('specialization_id', $filters['specialization_id']);
        }

        //filter by city
        if (!empty($filters['city'])) {
            $query->whereHas('hospital', function ($q) use ($filters) {
                $q->where('city', $filters['city']);
            });
        }

        //filter by consultation_fee
        if (!empty($filters['max_fee'])) {
            $query->where('consultation_fee', '<=', $filters['max_fee']);
        }

        //filter by rating
        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        //filter by video_consultation
        if (!empty($filters['video_consultation'])) {
            $query->where('video_consultation_available', true);
        }

        //search by name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        //sorting
        $sort_by = $filters['sort_by'] ?? 'rating';
        $sort_direction = $filters['sort_direction'] ?? 'desc';

        if ($sort_by === 'rating') {
            $query->orderBy('rating', $sort_direction);
        } elseif ($sort_by === 'fee') {
            $query->orderBy('consultation_fee', $sort_direction);
        } elseif ($sort_by === 'experience') {
            $query->orderBy('years_of_experience', $sort_direction);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    //get doctor details
    public function doctorDetails($doctor_id)
    {
        $doctor = Doctor::with([
            'specialization',
            'hospitals',
            'reviews' => function ($q) {
                $q->where('is_verified', true)
                    ->latest()
                    ->limit(10);
            }
        ])->findOrFail($doctor_id);

        return $doctor;
    }

    //create doctor
    public function createDoctor($data)
    {
        DB::beginTransaction();

        try {
            $is_Available = $this->checkDoctorAvailability(
                $data['license_number']
            );

            if (!$is_Available) {
                throw new Exception('Doctor with specfic license_number already exists');
            }
            //handle profile photo
            $profilePhotoPath = null;
            if (isset($data['profile_photo'])) {
                $profilePhotoPath = $data['profile_photo']->store('doctors/profiles', 'public');
            }
            //$doctor = Doctor::findOrFail($data['doctor_id']);

            //generate doctor
            $doctor = Doctor::create([
                'user_id' => $data['user_id'],
                'specialization_id' => $data['specialization_id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'license_number' => $data['license_number'],
                'years_of_experience' => $data['years_of_experience'],
                'qualification' => $data['qualification'],
                'bio' => $data['bio'] ?? '',
                'profile_photo' => $profilePhotoPath,
                'consultation_fee' => $data['consultation_fee'],
                'rating' => 0.0,
                'total_reviews' => 0,
                'hospital_affiliation' => $data['hospital_affiliation'] ?? '',
                'video_consultation_available' => $data['video_consultation_available'] ?? false,
                'languages_spoken' => json_encode($data['languages_spoken'] ?? []),
                'is_verified' => $data['is_verified'] ?? false,
                'is_available' => $data['is_available'] ?? true,
            ]);
            DB::commit();
            return $doctor;
        } catch (Exception $e) {
            DB::rollBack();
            //delete uploaded photo
            if (isset($profilePhotoPath) && $profilePhotoPath) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            throw $e;
        }
    }

    //checkcdoctor availabiblity
    public function checkDoctorAvailability($license_number)
    {
        $existing = Doctor::where('license_number', $license_number)
            ->exists();

        return !$existing;
    }


    //update doctor
    public function updateDoctor($doctor_id, $data)
    {
        DB::beginTransaction();
        try {
            $doctor = Doctor::findOrFail($doctor_id);
            //check license_number uniqness
            if (isset($data['license_number']) && $data['license_number'] != $doctor->license_number) {
                $is_Available = $this->checkDoctorAvailability($data['license_number']);
                if (!$is_Available) {
                    throw new Exception('Doctor with specified license_number already exists');
                }
            }

            //handle profile upload
            if (isset($data['profile_photo'])) {
                //delete old photo
                if ($doctor->profile_photo) {
                    Storage::disk('public')->delete($doctor->profile_photo);
                }
                $data['profile_photo'] = $data['profile_photo']->store('doctor/profiles', 'public');
            }

            if (isset($data['languages_spoken']) && is_array($data['languages_spoken'])) {
                $data['languages_spoken'] = json_encode($data['languages_spoken']);
            }

            $doctor->update($data);
            DB::commit();
            return $doctor->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //delete docotor
    public function deleteDoctor($doctor_id)
    {
        DB::beginTransaction();
        try {

            $doctor = Doctor::findOrFail($doctor_id);
            //delete uploaded photo
            if (isset($doctor->profile_photo)) {
                Storage::disk('public')->delete($doctor->profile_photo);
            }

            $doctor->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //top rated doctors
    public function getTopRatedDoctors($limit = 10)
    {
        return Doctor::with(['specialization', 'hospital'])
            ->where('is_verified', true)
            ->where('is_available', true)
            ->where('total_reviews', '>', 0)
            ->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    //Available doctors by specialization
    public function getAvailableDoctorsBySpecialization($specialization_id)
    {
        return Doctor::where('specialization_id', $specialization_id)
            ->where('is_verified', true)
            ->where('is_available', true)
            ->orderby('rating', 'desc')
            ->get();
    }

    //update Doctor Rating
    public function updateDoctorRating($doctor_id)
    {
        $doctor = Doctor::findOrFail($doctor_id);
        $averageRating = $doctor->reviews
            ->where('is_verified', true)
            ->avg('rating');

        $totalReviews = $doctor->reviews
            ->where('is_verified', true)
            ->count();

        $doctor->update([
            'rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews
        ]);
        return $doctor;
    }

    //toggle Availabiblity
    public function toggleAvailability($doctor_id)
    {
        $doctor = Doctor::findOrFail($doctor_id);
        $doctor->is_available = !$doctor->is_available;
        $doctor->save();
        return $doctor;
    }

    //verify Doctor
    public function verifyDoctor($doctor_id)
    {
        $doctor = Doctor::findOrFail($doctor_id);
        $doctor->is_verified = true;
        $doctor->save();
        return $doctor;
    }
}
