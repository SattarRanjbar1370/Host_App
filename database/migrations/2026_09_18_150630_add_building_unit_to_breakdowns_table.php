<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->string('building_unit')->nullable()->after('building_id'); // مثلاً ورودی ۲، پلاک ۵ یا نام برج
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            //
        });
    }
};
