<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()
                ->constrained('clients')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            // client_id = NULL ⇒ plac. Unikalność (container, client|plac) wymuszamy w aplikacji
            // bo MySQL traktuje NULL-e jako różne w kluczach unikalnych.
            $table->index(['container_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_stock');
    }
};
