<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pozycje magazynowe
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->integer('bales')->default(0);        // liczba belek (może być ujemna przy wydaniu)
            $table->decimal('weight_kg', 10, 2);
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

        // Pozycje załadunku (w trakcie)
        Schema::create('loading_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->integer('bales')->default(0);
            $table->decimal('weight_kg', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Pozycje dostaw i zamówień
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->enum('form', ['luz', 'belka']);
            $table->decimal('weight_kg', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->timestamps();
        });

        // Produkcja i inwentaryzacja
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->decimal('weight_kg', 10, 2);
            $table->timestamp('produced_at')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->cascadeOnDelete();
            $table->decimal('weight_kg', 10, 2);
            $table->text('reason')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('loading_items');
        Schema::dropIfExists('warehouse_items');
    }
};
