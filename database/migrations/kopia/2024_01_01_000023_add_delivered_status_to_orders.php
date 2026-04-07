<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL wymaga ALTER COLUMN dla zmiany ENUM
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'planned',
            'loaded',
            'weighed',
            'delivered',
            'closed',
            'tool'
        ) NOT NULL DEFAULT 'planned'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'planned',
            'in_progress',
            'weighed',
            'classified',
            'closed',
            'loading',
            'loaded',
            'tool'
        ) NOT NULL DEFAULT 'planned'");
    }
};
