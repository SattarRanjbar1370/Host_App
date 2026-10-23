<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'reg_number',
        'national_id',
        'phone',
        'logo_path',
        'logo_icon',
        'is_default',
    ];
}