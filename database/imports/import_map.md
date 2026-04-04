# Import Map – Migracja danych

Folder: `database/imports/`
Baza źródłowa: `mrowisko_stary`
Baza docelowa: `mrowisko_local` (lokalnie) / `mrowisko_prod` (serwer)

---

## Kolejność importu

| # | Plik | Tabela źródłowa | Tabela docelowa | Status | Uwagi |
|---|------|-----------------|-----------------|--------|-------|
| 01 | `01_users.sql` | `users` | `users` | ✅ Zaimportowane | Tomek i Czarny Pan do poprawki przy finalnym imporcie |
| 02 | `02_clients.sql` | `kontrahenci` | `clients` | ✅ Zaimportowane | Kraj domyślnie PL, brak BDO/email/phone – do uzupełnienia |
| 03 | `03_client_addresses_contacts.sql` | `kontrahenci_adresy` + `kontrahenci_kontakty` | `client_addresses` + `client_contacts` | ✅ Zaimportowane | Pomięto kontakty z dział=transport |
| 05 | `05_lieferscheins.sql` | `ls` | `lieferscheins` | ⏳ Do wykonania | kierunek mapowany na client_id przez UPPER(short_name) |
| 06 | `06_clients_country_de.sql` | – | `clients` | ✅ Zaimportowane | Ustawiono country=DE dla 13 odbiorców LS (LEIPA, EISEN, SPREMBERG, SANDERSDORF, LEHNICE, Glückstadt, Lilla Edet, Hohenwestedt, GREIZ, KROSTITZ, Sonae Arauco, TREBSEN) |
| 04 | `04_waste_fractions.sql` | `towary_grupy` + `towary` | `waste_fraction_groups` + `waste_fractions` | ⏳ Do wykonania | client_id dla KARCHEM i sells_as_luz do ręcznej korekty |

---

## Jak ponownie importować (przy przejściu na serwer)

1. Pobierz dump aktualnej bazy z serwera
2. Wgraj do lokalnej bazy `mrowisko_stary` (nadpisz)
3. Uruchom skrypty w kolejności od 01 do ostatniego
4. Sprawdź weryfikację na końcu każdego skryptu

---

## Awatary kierowców

Wgraj pliki PNG do `storage/app/public/drivers/` następnie uruchom:

```sql
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Sebastian.png'  WHERE id = 1;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Łukasz.png'     WHERE id = 2;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Vasyl.png'      WHERE id = 3;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Recykler.png'   WHERE id = 4;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Tomek.png'      WHERE id = 5;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Tadeusz.png'    WHERE id = 6;
UPDATE mrowisko_local.drivers SET avatar = 'drivers/Zewnetrzny.png' WHERE id = 7;
```

---

## Znane problemy do poprawki przy finalnym imporcie

- `users` id=14 (Tomek): `role_id` daje moduł `kierowca` zamiast `hakowiec`
- `users` id=18 (Czarny Pan): `role_id` daje moduł `plac` zamiast `czarnypan`
