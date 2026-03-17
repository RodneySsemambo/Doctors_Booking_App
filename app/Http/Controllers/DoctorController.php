<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Services\DoctorService;
use Exception;
use Illuminate\Http\Request;
use League\Csv\Query\Limit;

class DoctorController extends Controller
{

    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    //search doctor with filters
    public function index(Request $request)
    {

        try {

            $filters = $request->only([
                'specialization_id',
                'city',
                'max_fee',
                'min_rating',
                'video_consultation',
                'search',
                'sort_by',
                'sort_direction',
                'per_page'

            ]);
            $doctors = $this->doctorService->searchDoctors($filters);

            return response()->json([
                'success' => true,
                'data' => $doctors
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctors',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function show($doctor_id)
    {
        try {
            $doctor =   $this->doctorService->doctorDetails($doctor_id);

            return response()->json([
                'success' => true,
                'data' => $doctor

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(CreateDoctorRequest $request)
    {
        try {

            $doctor = $this->doctorService->createDoctor($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'doctor created successfully',
                'data' => $doctor
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to create doctor',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateDoctorRequest $request, $doctor_id)
    {
        try {

            $doctor = $this->doctorService->updateDoctor($doctor_id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully',
                'data' => $doctor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update doctor',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy($doctor_id)
    {

        try {

            $doctor = $this->doctorService->deleteDoctor($doctor_id);
            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully',

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to delete doctor'
            ], 404);
        }
    }

    public function toggleDoctorAvailability($doctor_id)
    {
        try {
            $doctor = $this->doctorService->toggleAvailability($doctor_id);

            return response()->json([
                'success' => true,
                'message' => 'Doctor availability updated',
                'data' => $doctor,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function doctorVerification($doctor_id)
    {

        try {
            $doctor = $this->doctorService->verifyDoctor($doctor_id);

            return response()->json([
                'success' => true,
                'message' => 'Doctor verified successfully',
                'data' => $doctor,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function doctorAvailabilityBySpecialization($specialization_id)
    {
        try {
            $doctors = $this->doctorService->getAvailableDoctorsBySpecialization($specialization_id);

            return response()->json([
                'success' => true,
                'data' => $doctors,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function topRatedDoctors(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $doctor = $this->doctorService->getTopRatedDoctors($limit);
            return response()->json([
                'success' => true,
                'data' => $doctor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to get top rated doctors'
            ], 404);
        }
    }
}
