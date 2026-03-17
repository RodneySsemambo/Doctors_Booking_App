<?php

namespace App\Services;

use App\Models\Hospital;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Json;

class HospitalService
{

    //create hospital
    public function createHospital($data)
    {
        DB::beginTransaction();
        try {
            $hospital = Hospital::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'city' => $data['city'],
                'country' => $data['country'],
                'latitude' => 0.0,
                'longtitude' => 0.0,
                'openning_hours' => json_encode($data['openning_hours']) ?? '',
                'facilities' => json_encode($data['facilities']) ?? '',
                'rating' => 0.0,
                'is_active' => $data['is_active'] ?? true
            ]);
            DB::commit();
            return $hospital;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //update hospital
    public function update($data, $hospital_id)
    {
        DB::beginTransaction();
        try {
            $hospital = Hospital::findOrFail($hospital_id);
            if (isset($data['openning_hours']) && is_array($data['openning_hours'])) {
                $data['openning_hours'] = json_encode($data['openning_hours']);
            }
            if (isset($data['facilities']) && is_array($data['facilities'])) {
                $data['openning_hours'] = json_encode($data['facilities']);
            }
            $hospital->update($data);
            DB::commit();
            return $hospital->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //delete hospital
    public function deleteHospital($hospital_id)
    {
        DB::beginTransaction();
        try {
            $hospital = Hospital::findOrFail($hospital_id);
            $hospital->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
