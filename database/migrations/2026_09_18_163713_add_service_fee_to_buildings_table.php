<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->boolean('has_monthly_fee')->default(false)->after('address'); // آیا حق سرویس ماهانه دارد؟
            $table->decimal('monthly_fee', 12, 0)->default(0)->after('has_monthly_fee'); // مبلغ ماهیانه
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['has_monthly_fee', 'monthly_fee']);
        });
    }
};