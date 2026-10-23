<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('building_user', function (Blueprint $table) {
            if (!Schema::hasColumn('building_user', 'is_excluded')) {
                $table->boolean('is_excluded')->default(false)->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('building_user', function (Blueprint $table) {
            $table->dropColumn('is_excluded');
        });
    }
};