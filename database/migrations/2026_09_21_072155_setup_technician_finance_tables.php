<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ۱. افزودن فیلد حقوق ماهانه ثابت به جدول پرسنل
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'base_salary')) {
                $table->decimal('base_salary', 15, 0)->default(0)->after('is_active');
            }
        });

        // ۲. افزودن فیلدهای سهم و پورسانت تکنسین به جدول کارها (breakdowns)
        Schema::table('breakdowns', function (Blueprint $table) {
            if (!Schema::hasColumn('breakdowns', 'technician_share_type')) {
                $table->string('technician_share_type', 20)->default('none')->after('amount'); // none, percentage, fixed
            }
            if (!Schema::hasColumn('breakdowns', 'technician_share_value')) {
                $table->decimal('technician_share_value', 15, 0)->default(0)->after('technician_share_type');
            }
            if (!Schema::hasColumn('breakdowns', 'technician_share_amount')) {
                $table->decimal('technician_share_amount', 15, 0)->default(0)->after('technician_share_value');
            }
        });

        // ۳. ساخت جدول اختصاصی ثبت فیش‌ها و واریزی‌های بانکی به تکنسین
        if (!Schema::hasTable('technician_payouts')) {
            Schema::create('technician_payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('amount', 15, 0);
                $table->string('paid_at', 20)->nullable();
                $table->string('tracking_code', 100)->nullable();
                $table->string('payment_type', 50)->default('salary'); // salary, commission, advance, other
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_payouts');

        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn(['technician_share_type', 'technician_share_value', 'technician_share_amount']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['base_salary']);
        });
    }
};