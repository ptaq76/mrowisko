<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annex7_shipments', function (Blueprint $table) {
            $table->id();

            // Pole 1 - Person who arranges the shipment
            $table->foreignId('arranger_id')->constrained('annex7_contractors');

            // Pole 2 - Importer/consignee (= pole 7 Recovery facility)
            $table->foreignId('importer_id')->constrained('annex7_contractors');

            // Pole 3 - puste, pomijamy

            // Pole 4 - Data wysyłki
            $table->date('date_shipment');

            // Pole 5 - First carrier
            $table->foreignId('carrier_id')->constrained('annex7_contractors');
            $table->date('carrier_date_transfer')->nullable();

            // Pole 6 - Waste generator
            $table->foreignId('generator_id')->constrained('annex7_contractors');

            // Pole 8 - Recovery operation
            $table->foreignId('recovery_operation_id')->constrained('annex7_recovery_operations');

            // Pole 9 - Usual description
            $table->foreignId('waste_description_id')->constrained('annex7_waste_descriptions');

            // Pole 10 - Waste identification (istniejąca tabela waste_codes)
            $table->foreignId('waste_code_id')->constrained('waste_codes');

            $table->enum('status', ['draft', 'generated'])->default('draft');
            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annex7_shipments');
    }
};
