<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakdownPart extends Model
{
    use HasFactory;

    protected $table = 'breakdown_parts';

    protected $fillable = [
        'breakdown_id',
        'part_name',
        'quantity',
        'unit_price',
        'total_price',
        'part_code',
    ];

    public function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
    }
}