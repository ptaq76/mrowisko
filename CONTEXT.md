# MrowiskoBIS – Kontekst projektu

## Stack technologiczny
- **Laravel 10**, PHP 8.3, MySQL (`mrowisko_local`), Bootstrap 5, XAMPP (Windows)
- **Pakiety**: `mpdf/mpdf`, `smalot/pdfparser`, `webklex/laravel-imap`
- **UI**: Bootstrap 5, Font Awesome, SweetAlert2, Barlow Condensed (font)
- Środowisko: `http://127.0.0.1:8000`
- **ext-intl** musi być włączony w `php.ini` (odkomentować `extension=intl`) – wymagane dla `Carbon::translatedFormat()`
- **Carbon locale** ustawiony globalnie w `AppServiceProvider::boot()`: `Carbon::setLocale('pl')`

---

## Architektura – moduły i role

| Moduł | Prefix | Middleware |
|---|---|---|
| Admin | `/admin` | `module:admin` |
| Biuro | `/biuro` | `module:biuro` |
| Kierowca | `/kierowca` | `module:kierowca` |
| Plac | `/plac` | `module:plac` |

Middleware `EnsureUserModule` chroni trasy. `users.id` = login (string, np. `admin`).

---

## Baza danych – tabele

### Podstawowe
- `users` – login (string id), moduł
- `clients` – kontrahenci (short_name, country, type: sale/pickup/both, is_active)
- `drivers` – kierowcy (user_id → users, tractor_id, trailer_id, color)
- `vehicles` – pojazdy (type: ciągnik/naczepa, plate, subtype, is_active)
- `vehicle_sets` – zestawy (tractor_id, trailer_id, tare_kg, label)
- `orders` – zlecenia

### Zlecenia (`orders`)
Kluczowe pola:
- `type`: `pickup` | `sale`
- `planned_date`, `planned_time`
- `plac_date` – kiedy zlecenie pojawia się w module Plac (nullable)
- `status`: `planned → loaded → weighed → closed` (sale) / `planned → weighed → delivered → closed` (pickup)
- `lieferschein_id`, `driver_id`, `client_id`, `start_client_id`, `tractor_id`, `trailer_id`
- `weight_brutto`, `weight_netto`, `weight_receiver`
- `is_archived` – **musi być w `$fillable` i `$casts` modelu Order** (boolean)

### Lieferschein
- `lieferscheins` – number (unique), importer_id, client_id, goods_id, waste_code_id, date, time_window, pdf_path, is_used
- `ls_goods` – towary LS
- `importers` – importerzy (name, is_active)
- `waste_codes` – kody odpadów (code unique, description, is_active)

### Magazyn / Załadunek
- `warehouse_items` – stan magazynu (fraction_id, bales, date, origin: production/loading/delivery/inventory, origin_order_id)
- `loading_items` – pozycje załadunku (order_id, fraction_id, bales)
- `waste_fractions` – frakcje odpadów (name, allows_belka, show_in_loadings, show_in_deliveries, is_active)
- `waste_fraction_groups`

### Ważenia
- `weighings` – ważenia ręczne (hauler_id, fraction_id, weight, date, is_archived, push_to_order, archive_after)
- `haulers` – woźacy (client_id, name)

### Paliwo
- `fuel_vehicle_groups` – grupy (nazwa, active): Plac/PlacTransport/Audi/Prywatne/TIR/System
- `fuel_vehicles` – pojazdy (nazwa, grupa_id, active) – 29 pojazdów, seeder: `FuelVehicleSeeder`
- `fuel_transactions` – transakcje (type: tankowanie/dostawa/inwentaryzacja, liters, tank_after, fuel_vehicle_id, operator)

### Reklamacje / Gewichtsmeldung
- `reklamacje` – (typ: reklamacja/gewichtsmeldung, lieferschein string, lieferschein_id FK nullable, masa_netto, mail_date, plik_masa, sciezka_pliku_masy)
- `reklamacje_bledy` – (mail_subject, blad, plik_1, plik_2, folder_temp, status: nowy/zweryfikowany/pominiety)

### Annex 7
- `annex7_contractors` – (name, short_name, role: arranger/importer/carrier/generator/recovery, address, contact, tel, mail)
- `annex7_recovery_operations` – operacje odzysku
- `annex7_waste_descriptions` – opisy odpadów
- `annex7_shipments` – dokumenty (arranger_id, importer_id, carrier_id, generator_id, recovery_id, recovery_operation_id, waste_description_id, waste_code_id, date_shipment, status: draft/generated, pdf_path)

### Raport wysyłek
- `wysylki_ceny` – (order_id unique FK, cena_eur)
- `wysylki_transport` – (order_id unique FK, przewoznik_id, cena_eur, recznie bool)
- `koszty_transportu` – cennik tras (start_id→clients, stop_id→clients, przewoznik_id, cena_eur, is_active)
- `przewoznicy` – (nazwa, is_active)

### Inne
- `agent_chats` – historia czatu AI admina

---

## Kontrolery

### Biuro
- `PlanningController` – index (plan dnia), planNaPlac → `view('biuro.reports.plan_na_plac')`
- `OrderController` – store, update (plac_date proporcjonalnie), destroy, show, setStatus, setPlacDate, modalData
- `LieferscheinController` – CRUD, uploadPdf, fetchFromMail (IMAP), viewPdf, getPdfFiles
- `WasteCodeController` – CRUD (JSON)
- `WeighingController` – CRUD, archive, fetchFromMail
- `HaulerController` – CRUD
- `ReportController` – loadings, loadingsArchived, deliveries, deliveriesArchived, weighings, revert, archive, archiveDelivery, unarchiveDelivery, revertDelivery, deleteWeighing
- `ReklamacjeController` – index (filtr typ), bledy, bladUpdate, showFile, fetchMail
- `RaportWysylekController` – index (filtry: miesiac, tydzien, date_from, date_to, importer_id, goods_id, waste_code_id), saveCena, saveCenaBulk, saveTransport, dopasujKosztyTransportu
- `KosztTransportuController` – CRUD koszty + CRUD przewoźnicy
- `FuelVehicleController` – CRUD (z grupami)
- `Annex7Controller` – index, create, store, show, generatePdf (mPDF), contractorData (AJAX)
- `ClientController`, `ImporterController`, `VehicleController`, `WasteFractionController`

### Plac
- `DashboardController` – dashboard, orders, loadingForm, loadingAdd/Edit/Store/Destroy, closeLoading
- `DeliveryController` – CRUD dostaw, close
- `FuelController` – index, store, destroy (tylko ostatnia transakcja)
- `LoadingController`, `WarehouseController`, `InventoryController`, `ProductionController`

### Kierowca
- `DashboardController` – index, weighForm, weighSave, weighConfirm, saveReceiverWeight, setStatus

### Admin
- `AdminController` – dashboard, driversIndex/Show, agentView, agentChat (Anthropic API), agentChatSave/Delete
- `UserController` – CRUD, resetPassword
- `Annex7ContractorController` – CRUD z filtrem roli (GET ?role=)
- `Annex7RecoveryOperationController`, `Annex7WasteDescriptionController`

---

## Serwisy

- `ImapLsService` – pobieranie PDF z `ls@iantra.pl` (h56.seohost.pl:993), native IMAP extension
- `ImapReklamacjeService` – pobieranie reklamacji (webklex/laravel-imap)
- `ImapGewichtsmeldungService` – pobieranie Gewichtsmeldung (webklex/laravel-imap)
- `PdfParserService` – parsowanie PDF (smalot/pdfparser):
  - `przetworzDwaBlobs()` – reklamacje: wyciąga LS (regex Format B: `Lieferschein / Positionsnummer\n{nr}`) i masę netto (trzecia liczba po `Loading quantity (net)`)
  - `przetworzGewichtsmeldung()` – Gewichtsmeldung: `Lieferschein: {nr}` i `Gewicht: {n} t`

---

## Komendy Artisan

- `ls:fetch-pdfs` – co 15 minut, IMAP ls@iantra.pl → `storage/app/public/attachments/`
- `reklamacje:przetwarzaj` – co 5 minut, obsługuje obie skrzynki (reklamacje + gewichtsmeldung)

### Kernel schedule
```php
$schedule->command('ls:fetch-pdfs')->everyFifteenMinutes();
$schedule->command('reklamacje:przetwarzaj')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
```

---

## Widoki – struktura

```
resources/views/
├── layouts/app.blade.php, kierowca.blade.php
├── admin/
│   ├── _nav.blade.php (z dropdownem Annex 7)
│   ├── dashboard, drivers, agent, users/
│   └── annex7/ contractors/, recovery_operations/, waste_descriptions/
├── biuro/
│   ├── _nav.blade.php
│   ├── dashboard.blade.php
│   ├── planning/ index, order_modal, order_modal_edit, order_modal_js, quick_buttons
│   ├── ls/ index, create, edit
│   ├── reports/ loadings, loadings_archived, deliveries, deliveries_archived, weighings, plan_na_plac
│   ├── raporty/ wysylki.blade.php
│   ├── reklamacje/ index, bledy
│   ├── waste_codes/ index
│   ├── koszty_transportu/ index
│   ├── fuel_vehicles/ index
│   ├── fractions/, haulers/, clients/, vehicles/, importers/, weighings/
│   └── annex7/ index, create, show, pdf, _contractor_fields, _detail_block
├── kierowca/ dashboard, weigh
└── plac/ dashboard, orders, loading_form, loading_add, delivery*, fuel, warehouse, inventory, production*
```

---

## Menu biuro (_nav.blade.php) – kolejność

1. **Planowanie** (fa-calendar-alt)
2. **Dokumenty** dropdown:
   - Annex 7 (fa-file-signature)
   - Reklamacje (fa-file-circle-exclamation)
   - Gewichtsmeldung (fa-file-circle-check)
3. **Lieferscheiny** (fa-file-alt)
4. **Ważenia** (fa-weight)
5. **Raporty** dropdown:
   - Plan na plac (fa-industry) ← przeniesiony z głównego menu
   - Wysyłki zagraniczne (fa-ship)
   - Załadunki (fa-truck-loading)
   - Dostawy (fa-boxes)
   - Ważenia kierowców (fa-weight)
6. **Ustawienia** dropdown: Towary | Woźacy | Kontrahenci | Pojazdy | Importerzy | Kody odpadów | Pojazdy–Paliwo | Koszty transportu

---

## Konfiguracja .env (kluczowe zmienne)

```env
# IMAP - LS
IMAP_HOST=h56.seohost.pl
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_USERNAME=ls@iantra.pl
IMAP_PASSWORD=Antra12!

# IMAP - Reklamacje
REKLAMACJE_IMAP_HOST=h56.seohost.pl
REKLAMACJE_IMAP_PORT=993
REKLAMACJE_IMAP_ENCRYPTION=ssl
REKLAMACJE_IMAP_VALIDATE_CERT=false
REKLAMACJE_IMAP_USERNAME=reklamacje@iantra.pl
REKLAMACJE_IMAP_PASSWORD=...

# IMAP - Gewichtsmeldung
GEWICHTSMELDUNG_IMAP_HOST=h56.seohost.pl
GEWICHTSMELDUNG_IMAP_PORT=993
GEWICHTSMELDUNG_IMAP_ENCRYPTION=ssl
GEWICHTSMELDUNG_IMAP_VALIDATE_CERT=false
GEWICHTSMELDUNG_IMAP_USERNAME=gewichtsmeldung@iantra.pl
GEWICHTSMELDUNG_IMAP_PASSWORD=...

# Anthropic (admin agent)
ANTHROPIC_API_KEY=sk-ant-...
```

---

## Konwencje i zasady projektu

### Blade
- Layout: `@extends('layouts.app')` z `@section('nav_menu') @include('modul._nav') @endsection`
- Skrypty: `@section('scripts')/@endsection` (nie push/endpush)
- SweetAlert2 dla wszystkich feedbacków i potwierdzeń
- **Styl nagłówka raportów**: klasy `report-header` + `report-title` (Barlow Condensed, 22px, 900, uppercase) + `badge-count` – identyczny we wszystkich raportach
- **Kolorystyka raportów**: załadunki `#f39c12`, dostawy `#27ae60`, ważenia `#3498db`, wysyłki/plan-na-plac `#1a1a1a`
- **Tabelka towarów** w raportach: `border:1px solid #e8eaed` na całej tabeli, linie między wierszami i kolumnami, `∑` zamiast słowa SUMA w wierszu sumy
- **Przyciski copy-date**: `btn-copy-date` z ikoną `fa-angle-double-right`, kolor zgodny z modułem

### Routing
- Trasy Annex7 słowników (admin.*) są w grupie `admin` z `middleware:module:admin`
- `contractor-data` MUSI być przed `/{annex7}` (unikanie konfliktu wildcard)
- Po każdej zmianie tras: `php artisan route:clear`
- Trasy raportów dostaw: `reports/deliveries/archived` i `reports/deliveries/{order}/unarchive` (dodane)

### PDF
- Pliki LS: `storage/app/public/{rok}/{tydzien}/{importer}_{numer}.pdf`
- Pliki reklamacji: `storage/app/reklamacje/{rok}/{miesiac}/{lieferschein}/{plik}`
- Błędy reklamacji: `storage/app/reklamacje/_bledy/{uniqid}/{plik}`
- PDF generowane przez `mpdf/mpdf` (Annex 7) – zastąpił DomPDF

### Znane problemy i rozwiązania
- `\$` w plikach PHP generowanych przez Python → fix: `re.sub(r'\\\$([a-zA-Z])', r'$\1', c)`
- `auth()->id()` zwraca string (login) → używamy `auth()->user()->id`
- Sesja wygasła (419) → `Handler.php` przekierowuje na login
- `Reklamacja` model wymaga `protected $table = 'reklamacje'`
- `plac_date` w `show()` musi być formatowane jako `Y-m-d` (nie Carbon ISO)
- `is_archived` w modelu `Order` **musi być w `$fillable`** – bez tego `update(['is_archived' => true])` jest cicho ignorowane
- `translatedFormat()` wymaga `ext-intl` – bez rozszerzenia zwraca angielskie nazwy; włączyć w `php.ini` XAMPP
- Elementy JS `#orderModalSubtitle` / `#editOrderModalSubtitle` zastąpione przez `#orderTypeBadge` / `#editOrderTypeBadge` – przy zmianach HTML modali zawsze sprawdzaj JS

### plac_date – logika
- `NULL` = zlecenie niewidoczne na placu
- Po zapisaniu nowego zlecenia SweetAlert pyta: `0` (ten sam dzień) lub `-1` (poprzedni dzień roboczy)
- Przy edycji zmiany daty zlecenia → `plac_date` przesuwa się proporcjonalnie
- Badge w widoku planowania: czarne kółko z cyfrą różnicy dni

### Datepicker jQuery UI
- CSS i JS jQuery UI **muszą być w `@section('scripts')`**, nie w `@section('styles')` – inaczej strzałki renderują się jako tekst "PrevNext"
- Konfiguracja: `showOtherMonths: true`, `selectOtherMonths: true`, `firstDay: 1`
- Wybrany dzień: klasa `selected-day` (pomarańczowy `#ff9900`)
- Dziś: klasa `ui-datepicker-today` (zielone obramowanie)
- Święta: klasa `highlight-red` z `/swieta.php`

---

## Migracje (kolejność)
```
000001-000022  – tabele podstawowe
000023         – add delivered status to orders
000024         – add is_archived to orders
000025         – add weight_receiver to orders
000026         – add show_in_sales to waste_fractions
000027         – create weighings table
000028         – create haulers table
000029         – add is_archived to weighings
000030         – create agent_chats table
000031         – create waste_codes table + waste_code_id do lieferscheins
000032         – create fuel_vehicle_groups, fuel_vehicles, fuel_transactions
2026_03_27_000001 – create reklamacje table
2026_03_27_000002 – create reklamacje_bledy table
2026_03_27_000003 – add lieferschein_id to reklamacje
2026_03_27_000004 – add typ to reklamacje
2026_04_01_000001 – create wysylki_ceny table
2026_04_01_000002 – create przewoznicy, koszty_transportu, wysylki_transport
2026_04_01_000003 – add plac_date to orders
```

---

## Do zrobienia (pending)
- Vite/HMR (omawiane, nie zaimplementowane)
- Import danych ze starej bazy
- Crontab na serwerze produkcyjnym
- Kolumny raportu wysyłek: Cena na placu, Wartość (puste – do uzupełnienia)
- Widok zbiorczy zleceń z powiązaniem LS ↔ reklamacja/gewichtsmeldung
