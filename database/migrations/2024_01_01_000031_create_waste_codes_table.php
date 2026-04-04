<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Dodaj waste_code_id do lieferscheins
        Schema::table('lieferscheins', function (Blueprint $table) {
            $table->foreignId('waste_code_id')
                  ->nullable()
                  ->after('time_window')
                  ->constrained('waste_codes')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lieferscheins', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\WasteCode::class);
            $table->dropColumn('waste_code_id');
        });
        Schema::dropIfExists('waste_codes');
    }
};
