<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_fractions', function (Blueprint $table) {
            if (!Schema::hasColumn('waste_fractions', 'show_in_sales')) {
                $table->boolean('show_in_sales')->default(true)->after('show_in_production');
            }
        });
    }

    public function down(): void
    {
        Schema::table('waste_fractions', function (Blueprint $table) {
            $table->dropColumn('show_in_sales');
        });
    }
};
