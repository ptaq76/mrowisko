<?php

use Illuminate\Database\Migrations\Migration;

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
