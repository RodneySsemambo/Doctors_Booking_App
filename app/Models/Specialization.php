<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    /** @use HasFactory<\Database\Factories\SepcializationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function doctor(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}
