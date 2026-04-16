<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historia zdarzeń kontenerów
        Schema::create('container_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')
                ->constrained('containers')
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->enum('event_type', [
                'wyjazd_z_placu',
                'zostawiony_u_klienta',
                'zabrany_od_klienta',
                'powrot_na_plac',
            ]);
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Sesje belowania (produkcja)
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->restrictOnDelete();
            $table->unsignedInteger('quantity');     // sztuki bel
            $table->decimal('weight_kg', 10, 2);
            $table->timestamp('produced_at')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Korekty inwentaryzacyjne
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->restrictOnDelete();
            $table->enum('form', ['luz', 'belka']);
            $table->unsignedInteger('quantity_before')->default(0);
            $table->unsignedInteger('quantity_after')->default(0);
            $table->decimal('weight_before', 10, 2)->default(0);
            $table->decimal('weight_after', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Wnioski handlowców o odbiór
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();
            $table->foreignId('salesman_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('requested_date')->nullable();
            $table->text('fractions_note')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['nowy', 'zrealizowany'])->default('nowy');
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Wizyty małych samochodów (tylko biuro, bez wpływu na magazyn)
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();
            $table->foreignId('waste_fraction_id')
                ->constrained('waste_fractions')
                ->restrictOnDelete();
            $table->decimal('weight_in', 10, 2)->nullable();   // wjazd
            $table->decimal('weight_out', 10, 2)->nullable();  // wyjazd
            // netto = weight_in - weight_out (obliczane dynamicznie)
            $table->timestamp('visited_at')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Karty BDO (pobierane automatycznie z systemu rządowego)
        Schema::create('bdo_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('bdo_number')->index(); // powiązanie z clients.bdo
            $table->string('waste_code')->nullable();
            $table->text('waste_description')->nullable();
            $table->decimal('planned_weight_kg', 10, 2)->nullable();
            $table->string('status')->nullable(); // status z systemu BDO
            $table->timestamp('issued_at')->nullable();
            $table->json('raw_data')->nullable(); // pełne dane z API
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bdo_cards');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('pickup_requests');
        Schema::dropIfExists('inventory_adjustments');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('container_events');
    }
};
