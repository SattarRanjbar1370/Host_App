<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'paid_at',
        'tracking_code',
        'payment_type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}