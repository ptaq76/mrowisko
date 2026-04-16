<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weighings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('weighed_at');             // data i godzina ważenia
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->foreignId('order_id')              // opcjonalne powiązanie ze zleceniem
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->string('plate1')->nullable();       // nr rej. pojazdu
            $table->string('plate2')->nullable();       // nr rej. naczepy
            $table->decimal('weight1', 8, 3)->nullable(); // waga 1 [t]
            $table->decimal('weight2', 8, 3)->nullable(); // waga 2 [t]
            $table->decimal('result', 8, 3)->nullable(); // wynik = weight1 - weight2
            $table->string('goods')->nullable();        // towar
            $table->text('notes')->nullable();          // uwagi
            $table->enum('source', ['driver', 'manual'])->default('manual'); // skąd pochodzi
            $table->foreignId('created_by_user')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weighings');
    }
};
