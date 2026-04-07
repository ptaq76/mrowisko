<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Przewoźnicy
        Schema::create('przewoznicy', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Koszty transportu
        Schema::create('koszty_transportu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('start_id')
                  ->constrained('clients')
                  ->restrictOnDelete();
            $table->foreignId('stop_id')
                  ->constrained('clients')
                  ->restrictOnDelete();
            $table->foreignId('przewoznik_id')
                  ->nullable()
                  ->constrained('przewoznicy')
                  ->nullOnDelete();
            $table->decimal('cena_eur', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Koszty transportu dla konkretnych wysyłek (wpisane ręcznie)
        Schema::create('wysylki_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->unique()
                  ->constrained('orders')
                  ->cascadeOnDelete();
            $table->foreignId('przewoznik_id')
                  ->nullable()
                  ->constrained('przewoznicy')
                  ->nullOnDelete();
            $table->decimal('cena_eur', 10, 2)->nullable();
            $table->boolean('recznie')->default(false); // true = wpisano ręcznie
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wysylki_transport');
        Schema::dropIfExists('koszty_transportu');
        Schema::dropIfExists('przewoznicy');
    }
};
