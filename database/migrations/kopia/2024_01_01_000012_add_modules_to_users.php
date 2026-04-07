<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL nie pozwala na ALTER COLUMN dla enum w prosty sposób
        // Modyfikujemy kolumnę module dodając nowe wartości
        DB::statement("ALTER TABLE users MODIFY COLUMN module ENUM(
            'admin',
            'biuro',
            'kierowca',
            'hakowiec',
            'plac',
            'handlowiec',
            'czarnypan',
            'karchem'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN module ENUM(
            'admin',
            'biuro',
            'kierowca',
            'hakowiec',
            'plac',
            'handlowiec'
        ) NOT NULL");
    }
};
