<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'name',
        'role_title',
        'phone',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}