## Instalacja DomPDF (jeśli jeszcze nie masz)

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## Dodanie do nawigacji _nav.blade.php (moduł Biuro)

```html
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('biuro.annex7.*') ? 'active' : '' }}"
       href="{{ route('biuro.annex7.index') }}">
        <i class="bi bi-file-earmark-text"></i> Annex 7
    </a>
</li>
```

## Struktura plików do skopiowania do projektu

```
app/
  Models/
    Annex7Contractor.php
    Annex7RecoveryOperation.php
    Annex7WasteDescription.php
    Annex7Shipment.php
  Http/Controllers/Biuro/
    Annex7Controller.php

database/migrations/
  2024_01_01_000001_create_annex7_contractors_table.php
  2024_01_01_000002_create_annex7_recovery_operations_table.php
  2024_01_01_000003_create_annex7_waste_descriptions_table.php
  2024_01_01_000004_create_annex7_shipments_table.php

resources/views/biuro/annex7/
  index.blade.php
  create.blade.php
  show.blade.php
  pdf.blade.php
  _detail_block.blade.php
  _contractor_fields.blade.php
```

## Seed przykładowych danych słownikowych

```php
// database/seeders/Annex7Seeder.php
Annex7RecoveryOperation::insert([
    ['code' => 'R1',  'description' => 'Use principally as a fuel or other means to generate energy'],
    ['code' => 'R3',  'description' => 'Recycling/reclamation of organic substances'],
    ['code' => 'R4',  'description' => 'Recycling/reclamation of metals and metal compounds'],
    ['code' => 'R13', 'description' => 'Storage of wastes pending recovery operations'],
]);

Annex7WasteDescription::insert([
    ['description' => 'Waste wood and wood products'],
    ['description' => 'Waste paper and cardboard'],
    ['description' => 'Plastic waste'],
    ['description' => 'Mixed municipal waste'],
]);
```

## Uruchomienie migracji

```bash
php artisan migrate
php artisan db:seed --class=Annex7Seeder
```
