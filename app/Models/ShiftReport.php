<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    use HasFactory;

    protected $table = 'shift_reports';

    protected $fillable = [
        'user_id',
        'shift_date',
        'shift_type',
        'workshop_status',
        'covered_buildings',
        'summary',
        'pending_issues',
        'manager_feedback',
    ];

    // تکنسین ثبت‌کننده شیفت
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}