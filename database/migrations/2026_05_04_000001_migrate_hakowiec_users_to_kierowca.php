<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('module', 'hakowiec')
            ->update(['module' => 'kierowca']);
    }

    public function down(): void
    {
        // brak revertu — nie pamiętamy którzy byli hakowcami
    }
};
