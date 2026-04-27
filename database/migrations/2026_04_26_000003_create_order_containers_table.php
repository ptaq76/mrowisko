<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('container_id')->constrained('containers')->restrictOnDelete();
            $table->enum('slot', ['tractor', 'trailer']);
            $table->enum('direction', ['pickup', 'drop']);
            $table->timestamps();

            $table->index(['order_id', 'slot', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_containers');
    }
};
