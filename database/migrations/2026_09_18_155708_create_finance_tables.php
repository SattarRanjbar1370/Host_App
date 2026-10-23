<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // قراردادهای سرویس ماهانه هر بلوک
        Schema::create('maintenance_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->decimal('monthly_fee', 12, 0); // مبلغ شارژ ماهیانه
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول واریزی‌ها و پرداختی‌های مدیران ساختمان
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users');
            $table->decimal('amount', 12, 0); // مبلغ واریزی
            $table->date('paid_at'); // تاریخ پرداخت
            $table->string('tracking_code')->nullable(); // شماره پیگیری یا فیش
            $table->text('description')->nullable(); // توضیحات
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('maintenance_contracts');
    }
};