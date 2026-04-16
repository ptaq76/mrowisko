<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_fractions', function (Blueprint $table) {
            $table->id();

            // Relacja do grupy (dodane w 000015)
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('waste_fraction_groups')
                ->nullOnDelete();

            // Podstawowe pola
            $table->string('name');
            $table->boolean('allows_luz')->default(true);
            $table->boolean('allows_belka')->default(true);
            $table->boolean('sells_as_luz')->default(false);

            // Flagi widoczności (dodane w 000015)
            $table->boolean('show_in_deliveries')->default(true);
            $table->boolean('show_in_loadings')->default(true);
            $table->boolean('show_in_production')->default(true);

            // Flaga widoczności w sprzedaży (dodane w 000026)
            $table->boolean('show_in_sales')->default(true);

            // Relacja do klienta
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_fractions');
    }
};
