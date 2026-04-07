<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela grup frakcji
        Schema::create('waste_fraction_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Dodaj kolumny do waste_fractions
        Schema::table('waste_fractions', function (Blueprint $table) {
            $table->foreignId('group_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('waste_fraction_groups')
                  ->nullOnDelete();
            $table->boolean('show_in_deliveries')->default(true)->after('sells_as_luz');
            $table->boolean('show_in_loadings')->default(true)->after('show_in_deliveries');
            $table->boolean('show_in_production')->default(true)->after('show_in_loadings');
        });
    }

    public function down(): void
    {
        Schema::table('waste_fractions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id', 'show_in_deliveries', 'show_in_loadings', 'show_in_production']);
        });
        Schema::dropIfExists('waste_fraction_groups');
    }
};
