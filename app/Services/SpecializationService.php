<?php

namespace App\Services;

use App\Models\Specialization;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Json;

class SpecializationService
{

    public function searchSpecialization($filters)
    {
        $query = Specialization::with(['name', 'description'])
            ->where('is_active', true);
        if (!empty($filters['name'])) {
            $query->where('name', $filters['name']);
        }
        if (!empty($filters['description'])) {
            $query->where('description', $filters['description']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createSpecialization($data)
    {
        DB::beginTransaction();
        try {
            $specialization = Specialization::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_active' => $data['is_active'] ?? true
            ]);
            DB::commit();
            return $specialization;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSpecialization($data, $specialization_id)
    {
        DB::beginTransaction();
        try {
            $specialization = Specialization::findOrFail($specialization_id);
            $specialization->update($data);
            DB::commit();
            return $specialization->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteSpecialization($specialization_id)
    {
        DB::beginTransaction();
        try {
            $specialization = Specialization::findOrFail($specialization_id);
            $specialization->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
