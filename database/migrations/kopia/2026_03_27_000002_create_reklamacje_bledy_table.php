<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reklamacje_bledy', function (Blueprint $table) {
            $table->id();
            $table->string('mail_subject')->nullable();
            $table->dateTime('mail_date')->nullable();
            $table->text('blad');                          // opis błędu (co się nie udało)
            $table->string('plik_1')->nullable();          // nazwa pierwszego załącznika
            $table->string('plik_2')->nullable();          // nazwa drugiego załącznika
            $table->string('folder_temp')->nullable();     // ścieżka do folderu z plikami
            $table->enum('status', ['nowy', 'zweryfikowany', 'pominiety'])->default('nowy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reklamacje_bledy');
    }
};
