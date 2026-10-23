<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_month', 7); // مثلاً: 1405/07
            $table->string('serviced_at', 20)->nullable(); // تاریخ انجام (شمسی)
            $table->boolean('is_completed')->default(false);
            $table->text('technician_note')->nullable();
            $table->timestamps();

            $table->unique(['building_id', 'service_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_services');
    }
};
