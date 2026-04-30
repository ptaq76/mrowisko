<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loading_item_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loading_item_id')
                ->constrained('loading_items')
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->string('path');
            $table->string('thumb_path');
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['loading_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loading_item_photos');
    }
};
