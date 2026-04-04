<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annex7_contractors', function (Blueprint $table) {
            $table->dropColumn('is_carrier');
            $table->enum('role', ['arranger', 'importer', 'carrier', 'generator'])->default('arranger')->after('mail');
        });
    }

    public function down(): void
    {
        Schema::table('annex7_contractors', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->boolean('is_carrier')->default(false);
        });
    }
};
