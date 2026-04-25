<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_packaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('opakowanie_id')->constrained('opakowania')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->nullable();         // ilość od kierowcy (nullable – plac może wpisać bez kierowcy)
            $table->unsignedInteger('qty_plac')->nullable();         // ilość potwierdzona/wpisana przez plac
            $table->foreignId('confirmed_by')->nullable()
                ->constrained('users')->nullOnDelete();              // kto potwierdził na placu
            $table->timestamp('confirmed_at')->nullable();           // kiedy potwierdził
            $table->timestamps();

            $table->unique(['order_id', 'opakowanie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_packaging');
    }
};