<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Importerzy
        Schema::create('importers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('country', ['PL', 'DE'])->default('DE');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Kody odpadów (wymagane przez lieferscheins i annex7)
        Schema::create('waste_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Grupy frakcji
        Schema::create('waste_fraction_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Frakcje odpadów
        Schema::create('waste_fractions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('allows_luz')->default(true);
            $table->boolean('allows_belka')->default(true);
            $table->boolean('sells_as_luz')->default(false);
            $table->boolean('show_in_deliveries')->default(true);
            $table->boolean('show_in_loadings')->default(true);
            $table->boolean('show_in_production')->default(true);
            $table->boolean('show_in_sales')->default(true);
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->foreignId('group_id')
                  ->nullable()
                  ->constrained('waste_fraction_groups')
                  ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Towary LS
        Schema::create('ls_goods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Lieferscheiny
        Schema::create('lieferscheins', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('importer_id')
                  ->nullable()
                  ->constrained('importers')
                  ->nullOnDelete();
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->foreignId('goods_id')
                  ->nullable()
                  ->constrained('ls_goods')
                  ->nullOnDelete();
            $table->foreignId('waste_code_id')
                  ->nullable()
                  ->constrained('waste_codes')
                  ->nullOnDelete();
            $table->date('date');
            $table->string('time_window')->nullable();
            $table->text('goods_description')->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('transp_zew')->default(false);
            $table->boolean('status')->default(false);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lieferscheins');
        Schema::dropIfExists('ls_goods');
        Schema::dropIfExists('waste_fractions');
        Schema::dropIfExists('waste_fraction_groups');
        Schema::dropIfExists('waste_codes');
        Schema::dropIfExists('importers');
    }
};
