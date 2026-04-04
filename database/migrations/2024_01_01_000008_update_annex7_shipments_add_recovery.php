<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annex7_shipments', function (Blueprint $table) {
            $table->foreignId('recovery_id')
                  ->nullable()
                  ->after('generator_id')
                  ->constrained('annex7_contractors');

        });
    }

    public function down(): void
    {
        Schema::table('annex7_shipments', function (Blueprint $table) {
            $table->dropForeign(['recovery_id']);
            $table->dropColumn('recovery_id');
        });
    }
};
