<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela towarów wysyłkowych (ls_goods)
        Schema::create('ls_goods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Zaktualizuj tabelę lieferscheins
        Schema::table('lieferscheins', function (Blueprint $table) {
            // Usuń stare kolumny i dodaj nowe
            $table->foreignId('goods_id')
                ->nullable()
                ->after('client_id')
                ->constrained('ls_goods')
                ->nullOnDelete();
            $table->boolean('transp_zew')->default(false)->after('is_used');
            $table->boolean('status')->default(false)->after('transp_zew');
            $table->string('pdf_path')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('lieferscheins', function (Blueprint $table) {
            $table->dropForeign(['goods_id']);
            $table->dropColumn(['goods_id', 'transp_zew', 'status', 'pdf_path']);
        });
        Schema::dropIfExists('ls_goods');
    }
};
