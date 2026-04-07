<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reklamacje', function (Blueprint $table) {
            $table->foreignId('lieferschein_id')
                  ->nullable()
                  ->after('lieferschein')
                  ->constrained('lieferscheins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reklamacje', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Lieferschein::class);
            $table->dropColumn('lieferschein_id');
        });
    }
};
