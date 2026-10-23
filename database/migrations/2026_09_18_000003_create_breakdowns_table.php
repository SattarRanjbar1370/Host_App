<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // ثبت‌کننده
            $table->string('title');
            $table->text('description')->nullable();
            
            // وضعیت فوریت و اجرا
            $table->enum('urgency', ['immediate', 'postponed'])->default('immediate'); // فوری / کارهای آینده
            $table->enum('status', ['pending_approval', 'in_progress', 'completed'])->default('pending_approval');
            
            // بخش مالی و تایید مدیر
            $table->boolean('is_billable')->default(false);
            $table->decimal('amount', 12, 0)->default(0); // مبلغ کار
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // یادداشت مدیر ارشد
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breakdowns');
    }
};