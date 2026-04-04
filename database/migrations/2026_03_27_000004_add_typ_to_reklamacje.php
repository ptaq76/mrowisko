<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reklamacje', function (Blueprint $table) {
            $table->enum('typ', ['reklamacja', 'gewichtsmeldung'])
                  ->default('reklamacja')
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('reklamacje', function (Blueprint $table) {
            $table->dropColumn('typ');
        });
    }
};
