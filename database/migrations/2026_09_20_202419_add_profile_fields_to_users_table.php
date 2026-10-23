<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'national_code')) {
                $table->string('national_code', 10)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'secondary_phone')) {
                $table->string('secondary_phone', 15)->nullable()->after('national_code');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->string('birth_date', 15)->nullable()->after('secondary_phone');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['national_code', 'secondary_phone', 'birth_date', 'is_active']);
        });
    }
};