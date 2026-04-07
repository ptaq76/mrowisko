<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela magazynu
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('fraction_id')
                  ->constrained('waste_fractions')
                  ->restrictOnDelete();
            $table->decimal('weight_kg', 10, 2);   // waga w kg
            $table->unsignedSmallInteger('bales');  // ilość belek
            $table->enum('origin', ['production', 'loading', 'delivery', 'inventory'])
                  ->default('production');
            $table->foreignId('origin_order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->nullOnDelete();
            $table->foreignId('operator_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Pozycje załadunku (wiele frakcji na jedno zlecenie)
        Schema::create('loading_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
            $table->foreignId('fraction_id')
                  ->constrained('waste_fractions')
                  ->restrictOnDelete();
            $table->unsignedSmallInteger('bales');   // ilość belek
            $table->decimal('weight_kg', 10, 2);     // szacunkowa waga (suma z magazynu)
            $table->text('notes')->nullable();
            $table->foreignId('operator_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loading_items');
        Schema::dropIfExists('warehouse_items');
    }
};
