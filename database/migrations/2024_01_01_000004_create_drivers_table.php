<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('name');
            $table->string('full_name');
            $table->string('firma', 100)->nullable();
            $table->string('color', 20);
            $table->string('phone')->nullable();
            $table->foreignId('tractor_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->foreignId('trailer_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};