<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reklamacje', function (Blueprint $table) {
            $table->id();
            $table->string('lieferschein');
            $table->decimal('masa_netto', 10, 3);
            $table->string('mail_subject')->nullable();
            $table->dateTime('mail_date')->nullable();
            $table->string('plik_lieferschein')->nullable(); // nazwa pliku z Lieferschein
            $table->string('plik_masa')->nullable();          // sama nazwa pliku z masą
            $table->string('sciezka_pliku_masy')->nullable(); // pełna ścieżka do zapisanego pliku z masą
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reklamacje');
    }
};
