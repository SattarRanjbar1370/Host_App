<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Building;
use App\Models\Breakdown;
use App\Models\TechnicianPayout;
use App\Models\Location;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'national_code',
        'secondary_phone',
        'birth_date',
        'password',
        'role',
        'is_active',
        'base_salary',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function payouts()
    {
        return $this->hasMany(TechnicianPayout::class);
    }

    public function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    public function excludedBuildings()
    {
        return $this->belongsToMany(Building::class, 'building_user')->wherePivot('is_excluded', true);
    }

    // ارتباط با بلوک‌ها/مناطق
    public function locations()
    {
        return $this->belongsToMany(\App\Models\Location::class, 'location_user', 'user_id', 'location_id');
    }

    // دریافت شناسه‌های بلوک‌های مجاز تکنسین
    public function getAllowedLocationIdsAttribute()
    {
        return $this->locations()->pluck('locations.id')->toArray();
    }

    // دریافت تمامی ساختمان‌ها/ورودی‌های مجاز تکنسین
    public function allowedBuildings()
    {
        $locationIds = $this->allowed_location_ids;
        return \App\Models\Building::whereIn('location_id', $locationIds);
    }
}