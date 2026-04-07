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
            
            // Relacje do klienta
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->restrictOnDelete();
            
            // Klient startowy dla zleceń transportowych (dodane w 000018)
            $table->foreignId('start_client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            
            // Kierowca - relacja do tabeli drivers (zmienione w 000018)
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('drivers')
                  ->nullOnDelete();
            
            // Pojazdy
            $table->foreignId('tractor_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            
            $table->foreignId('trailer_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            
            // Lieferschein
            $table->foreignId('lieferschein_id')
                  ->nullable()
                  ->constrained('lieferscheins')
                  ->nullOnDelete();
            
            // Daty
            $table->date('planned_date')->nullable();
            $table->date('plac_date')->nullable();  // dodane w 2026_04_01_000003
            $table->time('planned_time')->nullable();
            
            // Notatki
            $table->text('fractions_note')->nullable();
            $table->text('notes')->nullable();
            
            // Status (zmieniony w 000023 - dodano 'delivered', usunięto 'in_progress', 'classified', 'loading')
            $table->enum('status', [
                'planned',
                'loaded',
                'weighed',
                'delivered',
                'closed',
                'tool'
            ])->default('planned');
            
            // Flaga archiwizacji (dodane w 000024)
            $table->boolean('is_archived')->default(false);

            // Waga
            $table->decimal('weight_brutto', 10, 2)->nullable();
            $table->decimal('weight_netto', 10, 2)->nullable();
            $table->decimal('weight_receiver', 8, 3)->nullable();  // dodane w 000025
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

            // Utworzenie
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
