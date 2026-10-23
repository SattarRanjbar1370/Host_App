<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyService extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'user_id',
        'service_month',
        'serviced_at',
        'is_completed',
        'technician_note',
        'protected $fillable'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}