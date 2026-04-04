<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wysylki_ceny', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->unique()
                  ->constrained('orders')
                  ->cascadeOnDelete();
            $table->decimal('cena_eur', 10, 2)->nullable(); // cena sprzedaży €/t
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wysylki_ceny');
    }
};
