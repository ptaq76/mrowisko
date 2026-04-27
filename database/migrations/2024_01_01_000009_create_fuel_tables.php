<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_vehicle_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('fuel_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->foreignId('grupa_id')
                ->nullable()
                ->constrained('fuel_vehicle_groups')
                ->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->boolean('tracks_mileage')->default(false);
            $table->timestamps();
        });

        Schema::create('fuel_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['tankowanie', 'dostawa', 'inwentaryzacja']);
            $table->foreignId('fuel_vehicle_id')
                ->nullable()
                ->constrained('fuel_vehicles')
                ->nullOnDelete();
            $table->decimal('liters', 8, 2);
            $table->decimal('tank_after', 8, 2)->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->boolean('full_tank')->nullable();
            $table->string('operator')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_transactions');
        Schema::dropIfExists('fuel_vehicles');
        Schema::dropIfExists('fuel_vehicle_groups');
    }
};
