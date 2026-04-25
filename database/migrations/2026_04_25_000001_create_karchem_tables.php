<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karchem_kody_odpadow', function (Blueprint $table) {
            $table->id();
            $table->string('kod')->unique();
            $table->timestamps();
        });

        Schema::create('karchem_stany_poczatkowe', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('rok');
            $table->string('kod');
            $table->decimal('ilosc', 12, 3)->default(0);
            $table->timestamps();

            $table->unique(['rok', 'kod']);
        });

        Schema::create('karchem_wysylki', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('kod');
            $table->decimal('ilosc', 12, 3);
            $table->string('klient', 255);
            $table->timestamps();

            $table->index(['data', 'kod']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karchem_wysylki');
        Schema::dropIfExists('karchem_stany_poczatkowe');
        Schema::dropIfExists('karchem_kody_odpadow');
    }
};
