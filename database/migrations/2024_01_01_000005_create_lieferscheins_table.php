<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lieferscheins', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('importer_id')
                  ->constrained('importers')
                  ->restrictOnDelete();
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->restrictOnDelete();
            $table->date('date');
            $table->string('time_window')->nullable();
            $table->text('goods_description')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lieferscheins');
    }
};
