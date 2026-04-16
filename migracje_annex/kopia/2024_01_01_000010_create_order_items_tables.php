<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pozycje dostawy (klasyfikacja frakcji przy odbiorze)
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->restrictOnDelete();
            $table->enum('form', ['luz', 'belka']);
            $table->decimal('weight_kg', 10, 2)->default(0);
            $table->timestamps();
        });

        // Pozycje załadunku (wysyłka – bele)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(0); // sztuki bel
            $table->decimal('weight_kg', 10, 2)->nullable();  // po raporcie kierowcy
            $table->timestamps();
        });

        // Kontenery w zleceniu hakowca
        Schema::create('order_containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('container_id')
                ->constrained('containers')
                ->restrictOnDelete();
            $table->enum('action', ['zostawiony', 'zabrany']);
            $table->enum('location_from', ['plac', 'klient', 'transport'])->nullable();
            $table->enum('location_to', ['plac', 'klient', 'transport'])->nullable();
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->decimal('weight_brutto', 10, 2)->nullable();
            $table->decimal('weight_netto', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_containers');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('delivery_items');
    }
};
