<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_sets', function (Blueprint $table) {
            $table->id();
            $table->string('label');           // np. "PNT81294 / WGM5564P"
            $table->foreignId('tractor_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->foreignId('trailer_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->decimal('tare_kg', 8, 3);  // tara w tonach, np. 14.000
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_sets');
    }
};
