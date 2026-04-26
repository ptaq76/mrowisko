<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zadania', function (Blueprint $table) {
            $table->id();
            $table->text('tresc');
            $table->date('data')->index();
            $table->enum('target', ['driver', 'plac', 'all_drivers']);
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->uuid('batch_id')->nullable()->index();
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['data', 'driver_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zadania');
    }
};
