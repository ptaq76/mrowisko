<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kontrahenci Annex7 (z rolą przypisaną bezpośrednio do rekordu)
        Schema::create('annex7_contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('role', ['arranger', 'importer', 'carrier', 'generator', 'recovery']);
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Operacje odzysku (R1, R3 itd.)
        Schema::create('annex7_recovery_operations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->text('description');
            $table->timestamps();
        });

        // Opisy odpadów
        Schema::create('annex7_waste_descriptions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->text('description');
            $table->timestamps();
        });

        // Dokumenty Annex7 (wysyłki)
        Schema::create('annex7_shipments', function (Blueprint $table) {
            $table->id();

            // Pole 1 - osoba organizująca wysyłkę
            $table->foreignId('arranger_id')
                  ->constrained('annex7_contractors');

            // Pole 2 - importer/odbiorca (= pole 7 Recovery facility)
            $table->foreignId('importer_id')
                  ->constrained('annex7_contractors');

            // Pole 4 - data wysyłki
            $table->date('date_shipment');

            // Pole 5 - pierwszy przewoźnik
            $table->foreignId('carrier_id')
                  ->constrained('annex7_contractors');
            $table->date('carrier_date_transfer')->nullable();

            // Pole 6 - wytwórca odpadów
            $table->foreignId('generator_id')
                  ->constrained('annex7_contractors');

            // Pole 7 - zakład odzysku
            $table->foreignId('recovery_id')
                  ->nullable()
                  ->constrained('annex7_contractors');

            // Pole 8 - operacja odzysku
            $table->foreignId('recovery_operation_id')
                  ->constrained('annex7_recovery_operations');

            // Pole 9 - opis odpadu
            $table->foreignId('waste_description_id')
                  ->constrained('annex7_waste_descriptions');

            // Pole 10 - kod odpadu
            $table->foreignId('waste_code_id')
                  ->constrained('waste_codes');

            $table->enum('status', ['draft', 'generated'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annex7_shipments');
        Schema::dropIfExists('annex7_waste_descriptions');
        Schema::dropIfExists('annex7_recovery_operations');
        Schema::dropIfExists('annex7_contractors');
    }
};