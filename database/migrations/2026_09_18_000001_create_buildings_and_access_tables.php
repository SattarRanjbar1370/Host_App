<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول اطلاعات ساختمان‌ها و بلوک‌ها
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(); // مانند: A4, B3
            $table->string('title');            // عنوان کامل: بلوک A4 یا سرویس‌های خارج اکباتان
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // جدول دسترسی هر تکنسین به بلوک‌های مجاز
        Schema::create('building_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_user');
        Schema::dropIfExists('buildings');
    }
};