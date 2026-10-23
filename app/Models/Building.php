<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\Breakdown;
use App\Models\User;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'code',
        'title',
        'address',
        'has_monthly_fee',
        'monthly_fee',
    ];

    public function getFullAddressAttribute()
    {
        $locAddress = $this->location?->address;
        if ($this->address) {
            return $locAddress ? "{$locAddress}، {$this->address}" : $this->address;
        }
        return $locAddress ?? 'ثبت نشده';
    }

    protected function casts(): array
    {
        return [
            'has_monthly_fee' => 'boolean',
            'monthly_fee' => 'decimal:0',
        ];
    }

    // مخاطبان این بلوک (مدیر، نگهبان، پیمانکار)
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // لیست خرابی‌ها و کارهای این بلوک
    public function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    // تکنسین‌های دارای دسترسی به این بلوک
    public function users()
    {
        return $this->belongsToMany(User::class, 'building_user');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function contracts()
    {
        return $this->hasMany(MaintenanceContract::class);
    }

    // محاسبه مانده حساب (بدهی کارهای هزینه‌دار منهای واریزی‌ها)
    public function getBalanceAttribute()
    {
        $totalCost = $this->breakdowns()->where('approval_status', 'approved')->sum('amount');
        $totalPaid = $this->payments()->sum('amount');

        return $totalPaid - $totalCost; // عدد منفی یعنی بدهکار، مثبت یعنی بستانکار
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function monthlyServices()
    {
        return $this->hasMany(MonthlyService::class);
    }
}
