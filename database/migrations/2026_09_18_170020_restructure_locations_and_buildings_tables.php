<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ۱. جدول محدوده‌ها و بلوک‌ها (پدر)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // مثلاً: بلوک A4 یا سرویس‌های خارج اکباتان
            $table->string('code')->nullable(); // مثلاً: A4
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ۲. اتصال دسترسی پرسنل به بلوک‌ها
        Schema::create('location_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        // ۳. اضافه کردن کلید خارجی محدوده به جدول ساختمان‌ها/ورودی‌ها
        Schema::table('buildings', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('id')->constrained('locations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
        Schema::dropIfExists('location_user');
        Schema::dropIfExists('locations');
    }
};