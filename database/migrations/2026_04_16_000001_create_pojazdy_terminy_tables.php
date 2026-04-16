<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Słownik pojazdów / obiektów
        Schema::create('pojazdy_terminy', function (Blueprint $table) {
            $table->id();
            $table->string('nr_rej', 20)->unique();
            $table->string('rodzaj', 50);
            $table->string('marka', 50);
            $table->string('wlasciciel', 100);
            $table->string('vin', 50)->unique()->nullable();
            $table->year('rok_prod')->nullable();
            $table->string('opis', 255)->nullable();
            $table->timestamps();
        });

        // Akcje / terminy dla pojazdów
        Schema::create('pojazdy_terminy_akcje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pojazd_id')->constrained('pojazdy_terminy')->cascadeOnDelete();
            $table->string('action_type', 100);   // wolny tekst, np. "Przegląd OC"
            $table->date('completed_date')->nullable(); // kiedy wykonano
            $table->date('deadline_date')->nullable();  // termin następnego
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pojazdy_terminy_akcje');
        Schema::dropIfExists('pojazdy_terminy');
    }
};
