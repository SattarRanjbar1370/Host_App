<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            if (!Schema::hasColumn('breakdowns', 'technician_note')) {
                $table->text('technician_note')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('technician_note');
        });
    }
};