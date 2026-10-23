<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'building_id',
        'location_text',
        'steps',
        'team_members',
        'contract_amount',
        'status',
        'final_report',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'steps' => 'array',
        'team_members' => 'array',
        'contract_amount' => 'float',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}