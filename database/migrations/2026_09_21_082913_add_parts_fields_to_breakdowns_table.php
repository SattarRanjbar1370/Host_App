<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            if (!Schema::hasColumn('breakdowns', 'part_name')) {
                $table->string('part_name', 150)->nullable()->after('title');
                $table->decimal('part_price', 15, 0)->default(0)->after('part_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn(['part_name', 'part_price']);
        });
    }
};
