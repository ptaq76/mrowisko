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
            $table->enum('typ', ['reklamacja', 'gewichtsmeldung'])->default('reklamacja');
            $table->string('lieferschein');
            $table->foreignId('lieferschein_id')
                  ->nullable()
                  ->constrained('lieferscheins')
                  ->nullOnDelete();
            $table->decimal('masa_netto', 10, 3);
            $table->string('mail_subject')->nullable();
            $table->dateTime('mail_date')->nullable();
            $table->string('plik_lieferschein')->nullable();
            $table->string('plik_masa')->nullable();
            $table->string('sciezka_pliku_masy')->nullable();
            $table->timestamps();
        });

        Schema::create('reklamacje_bledy', function (Blueprint $table) {
            $table->id();
            $table->string('mail_subject')->nullable();
            $table->dateTime('mail_date')->nullable();
            $table->text('blad');
            $table->string('plik_1')->nullable();
            $table->string('plik_2')->nullable();
            $table->string('folder_temp')->nullable();
            $table->enum('status', ['nowy', 'zweryfikowany', 'pominiety'])->default('nowy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reklamacje_bledy');
        Schema::dropIfExists('reklamacje');
    }
};
