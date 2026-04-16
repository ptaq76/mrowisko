<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annex7', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('import_date');
            $table->string('importer_country', 2);
            $table->string('export_country', 2);
            $table->foreignId('recovery_operation_id')->nullable()->constrained('annex7_recovery_operations')->onDelete('set null');
            $table->foreignId('waste_description_id')->nullable()->constrained('annex7_waste_descriptions')->onDelete('set null');
            $table->decimal('quantity_kg', 12, 2);
            $table->text('packaging_type')->nullable();
            $table->text('means_of_transport')->nullable();
            $table->date('actual_shipment_date')->nullable();
            $table->text('special_requirements')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annex7');
    }
};
