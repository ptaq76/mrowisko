<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annex7_contractor_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annex7_id')->constrained('annex7')->onDelete('cascade');
            $table->foreignId('contractor_id')->constrained('annex7_contractors')->onDelete('cascade');
            $table->enum('role', ['arranger', 'importer', 'carrier', 'generator', 'recovery']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annex7_contractor_roles');
    }
};
