<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ciągnik', 'naczepa', 'solo']);
            $table->enum('subtype', ['hakowiec', 'firana', 'walking_floor'])->nullable();
            $table->string('plate')->unique();
            $table->string('brand')->nullable();
            $table->decimal('tare_kg', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicle_sets', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->foreignId('tractor_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->foreignId('trailer_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->decimal('tare_kg', 8, 3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_sets');
        Schema::dropIfExists('vehicles');
    }
};
