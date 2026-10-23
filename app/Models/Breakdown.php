<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Building;
use App\Models\User;

class Breakdown extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id',
        'building_unit',
        'user_id',
        'title',
        'technician_note',
        'description',
        'urgency',
        'status',
        'is_billable',
        'amount',
        'approval_status',
        'admin_note',
        'part_name',              // ✅ اضافه شد
        'part_count',             // ✅ اضافه شد
        'part_price',             // ✅ اضافه شد
        'technician_share_type',  // ✅ اضافه شد
        'technician_share_value', // ✅ اضافه شد
        'technician_share_amount',// ✅ اضافه شد
    ];

    protected function casts(): array
    {
        return [
            'is_billable' => 'boolean',
            'amount' => 'decimal:0',
            'part_count' => 'integer',
        ];
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function parts()
    {
        return $this->hasMany(\App\Models\BreakdownPart::class, 'breakdown_id');
    }
}
