<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // نام ثبتی کامل
            $table->string('brand')->nullable(); // نام تجاری/مختصر
            $table->string('reg_number')->nullable(); // شماره ثبت
            $table->string('national_id')->nullable(); // شناسه ملی
            $table->string('phone')->nullable(); // تلفن تماس
            $table->string('logo_path')->nullable(); // مسیر فایل لوگوی آپلودشده
            $table->string('logo_icon')->default('🛗'); // آیکون پیش‌فرض
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
