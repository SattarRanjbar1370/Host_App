<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'code', 'address'];

    // ساختمان‌ها و ورودی‌های داخل این بلوک
    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    // تکنسین‌های مجاز برای این محدوده
    public function users()
    {
        return $this->belongsToMany(User::class, 'location_user');
    }
}