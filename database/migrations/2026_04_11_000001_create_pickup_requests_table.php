<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();
            $table->foreignId('salesman_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->date('requested_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['nowe', 'przyjete', 'zrealizowane', 'anulowane', 'odrzucone_biuro'])->default('nowe');
            $table->timestamps();
        });

        Schema::create('pickup_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_request_id')
                ->constrained('pickup_requests')
                ->cascadeOnDelete();
            $table->string('nazwa');
            $table->decimal('cena', 10, 2)->nullable();
            $table->string('ilosc')->nullable(); // np. "3 tony", "2 belki"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_request_items');
        Schema::dropIfExists('pickup_requests');
    }
};
