<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pickup', 'sale']);
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->restrictOnDelete();
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('tractor_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->foreignId('trailer_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->foreignId('lieferschein_id')
                  ->nullable()
                  ->constrained('lieferscheins')
                  ->nullOnDelete();
            $table->date('planned_date')->nullable();
            $table->time('planned_time')->nullable();
            $table->text('fractions_note')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [
                // pickup statuses
                'planned',
                'in_progress',
                'weighed',
                'classified',
                'closed',
                // sale statuses
                'loading',
                'loaded',
            ])->default('planned');

            // Waga
            $table->decimal('weight_brutto', 10, 2)->nullable();
            $table->decimal('weight_netto', 10, 2)->nullable();
            $table->decimal('weight_original', 10, 2)->nullable(); // przed korektą biura
            $table->text('driver_notes')->nullable();
            $table->timestamp('confirmed_at_client')->nullable();

            // Akceptacja wagi przez biuro
            $table->foreignId('weight_accepted_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('weight_accepted_at')->nullable();

            // Przyjęcia bez zlecenia
            $table->boolean('is_unplanned')->default(false);
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
