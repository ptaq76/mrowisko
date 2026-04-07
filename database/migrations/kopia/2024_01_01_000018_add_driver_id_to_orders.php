<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Usuń stary FK driver_id → users
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
            });
        } catch (\Exception $e) {}

        // Dodaj start_client_id jeśli nie istnieje
        if (!Schema::hasColumn('orders', 'start_client_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('start_client_id')
                      ->nullable()
                      ->after('driver_id')
                      ->constrained('clients')
                      ->nullOnDelete();
            });
        }

        // Nowy FK driver_id → drivers
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('driver_id')
                  ->references('id')
                  ->on('drivers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['start_client_id']);
            $table->dropColumn('start_client_id');
        });
    }
};
