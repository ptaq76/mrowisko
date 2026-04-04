<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annex7_contractors', function (Blueprint $table) {
            $table->dropColumn('means_of_transport');
            $table->string('short_name', 50)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('annex7_contractors', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->string('means_of_transport')->nullable();
        });
    }
};
