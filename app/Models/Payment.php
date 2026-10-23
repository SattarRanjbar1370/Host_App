<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'building_id',
        'recorded_by',
        'amount',
        'paid_at',
        'tracking_code',
        'description',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}