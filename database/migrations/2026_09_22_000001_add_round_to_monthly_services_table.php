<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_services', function (Blueprint $table) {
            // حذف کلید یکتای قبلی که مانع ثبت چندباره بود
            $table->dropUnique(['building_id', 'service_month']);

            // اضافه کردن نوبت سرویس (پیش‌فرض نوبت ۱)
            if (!Schema::hasColumn('monthly_services', 'service_round')) {
                $table->unsignedTinyInteger('service_round')->default(1)->after('service_month');
            }

            // کلید یکتای جدید: هر ساختمان در هر ماه و در هر نوبت فقط یک رکورد
            $table->unique(['building_id', 'service_month', 'service_round'], 'b_m_r_unique');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_services', function (Blueprint $table) {
            $table->dropUnique('b_m_r_unique');
            $table->dropColumn('service_round');
            $table->unique(['building_id', 'service_month']);
        });
    }
};