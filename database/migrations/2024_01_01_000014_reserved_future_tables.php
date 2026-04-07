<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracja rezerwowa – placeholder na przyszłe tabele.
 * Aktualnie nie tworzy żadnych tabel.
 *
 * Kandydaci do dodania w przyszłości:
 *   - pickup_requests  (wnioski o odbiór)
 *   - visits           (wizyty)
 *   - bdo_entries      (wpisy BDO)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Zarezerwowane
    }

    public function down(): void
    {
        // Zarezerwowane
    }
};
