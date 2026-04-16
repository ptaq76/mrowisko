<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->string('street');
            $table->string('hours')->nullable();   // godziny odbioru
            $table->text('notes')->nullable();
            $table->integer('distance_km')->nullable();
            $table->decimal('latitude', 12, 7)->nullable();
            $table->decimal('longitude', 12, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_addresses');
    }
};
