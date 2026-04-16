<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('nip')->nullable();
            $table->string('bdo')->nullable();
            $table->enum('country', ['PL', 'DE'])->default('PL');
            $table->enum('type', ['pickup', 'sale', 'both'])->default('both');
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('salesman_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->enum('category', ['awizacje', 'faktury', 'handlowe']);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->string('street');
            $table->string('hours')->nullable();
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
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('clients');
    }
};
