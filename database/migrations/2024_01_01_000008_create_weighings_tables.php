<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('haulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('weighings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('weighed_at');
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->foreignId('hauler_id')
                ->nullable()
                ->constrained('haulers')
                ->nullOnDelete();
            $table->string('plate1')->nullable();
            $table->string('plate2')->nullable();
            $table->decimal('weight1', 8, 3)->nullable();
            $table->decimal('weight2', 8, 3)->nullable();
            $table->decimal('result', 8, 3)->nullable();
            $table->string('goods')->nullable();
            $table->text('notes')->nullable();
            $table->enum('source', ['driver', 'manual'])->default('manual');
            $table->boolean('is_archived')->default(false);
            $table->foreignId('created_by_user')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weighings');
        Schema::dropIfExists('haulers');
    }
};
