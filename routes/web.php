<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ── AUTH ──────────────────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', fn() => redirect()->route('login'));

// ── ADMIN ─────────────────────────────────────────────────────────────────────

Route::prefix('admin')
    ->middleware(['auth', 'module:admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/drivers', [\App\Http\Controllers\Admin\AdminController::class, 'driversIndex'])->name('drivers.index');
        Route::get('/drivers/{driver}', [\App\Http\Controllers\Admin\AdminController::class, 'driversShow'])->name('drivers.show');
        Route::get('/agent', [\App\Http\Controllers\Admin\AdminController::class, 'agentView'])->name('agent');
        Route::post('/agent/chat', [\App\Http\Controllers\Admin\AdminController::class, 'agentChat'])->name('agent.chat');
        Route::post('/agent/save', [\App\Http\Controllers\Admin\AdminController::class, 'agentChatSave'])->name('agent.save');
        Route::delete('/agent/{chat}', [\App\Http\Controllers\Admin\AdminController::class, 'agentChatDelete'])->name('agent.delete');

        // Annex 7 - słowniki
        Route::resource('annex7-contractors',         \App\Http\Controllers\Admin\Annex7ContractorController::class)->names('annex7-contractors');
        Route::resource('annex7-recovery-operations', \App\Http\Controllers\Admin\Annex7RecoveryOperationController::class)->names('annex7-recovery-operations');
        Route::resource('annex7-waste-descriptions',  \App\Http\Controllers\Admin\Annex7WasteDescriptionController::class)->names('annex7-waste-descriptions');

        // CRUD użytkowników
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Reset hasła – osobna trasa PATCH
        Route::patch('users/{user}/password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
             ->name('users.password');
    });

// ── BIURO ─────────────────────────────────────────────────────────────────────

Route::prefix('biuro')
    ->middleware(['auth', 'module:biuro'])
    ->name('biuro.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('biuro.dashboard'))->name('dashboard');

        // Planowanie
        Route::get('planning', [\App\Http\Controllers\Biuro\PlanningController::class, 'index'])->name('planning.index');
        Route::get('orders/modal-data', [\App\Http\Controllers\Biuro\OrderController::class, 'modalData'])->name('orders.modalData');
        Route::get('orders/quick-buttons', [\App\Http\Controllers\Biuro\OrderController::class, 'quickButtons'])->name('orders.quickButtons');
        Route::post('orders/quick-buttons', [\App\Http\Controllers\Biuro\OrderController::class, 'quickButtonStore'])->name('orders.quickButtons.store');
        Route::put('orders/quick-buttons/{button}', [\App\Http\Controllers\Biuro\OrderController::class, 'quickButtonUpdate'])->name('orders.quickButtons.update');
        Route::delete('orders/quick-buttons/{button}', [\App\Http\Controllers\Biuro\OrderController::class, 'quickButtonDestroy'])->name('orders.quickButtons.destroy');
        Route::post('orders/{order}/status', [\App\Http\Controllers\Biuro\OrderController::class, 'setStatus'])->name('orders.status');
        Route::get('orders/{order}', [\App\Http\Controllers\Biuro\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders', [\App\Http\Controllers\Biuro\OrderController::class, 'store'])->name('orders.store');
        Route::put('orders/{order}', [\App\Http\Controllers\Biuro\OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [\App\Http\Controllers\Biuro\OrderController::class, 'destroy'])->name('orders.destroy');

        // Lieferschein
        Route::get('ls/pdf-files', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'getPdfFiles'])->name('ls.pdfFiles');
        Route::get('ls/create', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'create'])->name('ls.create');
        Route::get('ls', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'index'])->name('ls.index');
        Route::post('ls/fetch-mail', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'fetchFromMail'])->name('ls.fetch-mail');
        Route::post('ls', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'store'])->name('ls.store');
        Route::get('ls/{lieferschein}/edit', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'edit'])->name('ls.edit');
        Route::put('ls/{lieferschein}', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'update'])->name('ls.update');
        Route::post('ls/{lieferschein}/status', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'markStatus'])->name('ls.status');
        Route::delete('ls/{lieferschein}', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'destroy'])->name('ls.destroy');
        Route::get('ls/{lieferschein}/pdf', [\App\Http\Controllers\Biuro\LieferscheinController::class, 'viewPdf'])->name('ls.pdf');

        // Importerzy
        Route::resource('importers', \App\Http\Controllers\Biuro\ImporterController::class)->only(['index', 'store', 'update']);

        // Frakcje odpadów
        Route::get('fractions/check-name', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'checkName'])->name('fractions.checkName');
        // Pojazdy paliwowe
        Route::get('fuel-vehicles', [\App\Http\Controllers\Biuro\FuelVehicleController::class, 'index'])->name('fuel-vehicles.index');
        Route::post('fuel-vehicles', [\App\Http\Controllers\Biuro\FuelVehicleController::class, 'store'])->name('fuel-vehicles.store');
        Route::put('fuel-vehicles/{fuelVehicle}', [\App\Http\Controllers\Biuro\FuelVehicleController::class, 'update'])->name('fuel-vehicles.update');
        Route::delete('fuel-vehicles/{fuelVehicle}', [\App\Http\Controllers\Biuro\FuelVehicleController::class, 'destroy'])->name('fuel-vehicles.destroy');
        Route::post('fuel-vehicles/{fuelVehicle}/toggle', [\App\Http\Controllers\Biuro\FuelVehicleController::class, 'toggle'])->name('fuel-vehicles.toggle');

        // Kody odpadów
        Route::get('waste-codes', [\App\Http\Controllers\Biuro\WasteCodeController::class, 'index'])->name('waste-codes.index');
        Route::post('waste-codes', [\App\Http\Controllers\Biuro\WasteCodeController::class, 'store'])->name('waste-codes.store');
        Route::put('waste-codes/{wasteCode}', [\App\Http\Controllers\Biuro\WasteCodeController::class, 'update'])->name('waste-codes.update');
        Route::delete('waste-codes/{wasteCode}', [\App\Http\Controllers\Biuro\WasteCodeController::class, 'destroy'])->name('waste-codes.destroy');

        // Woźacy
        Route::get('haulers', [\App\Http\Controllers\Biuro\HaulerController::class, 'index'])->name('haulers.index');
        Route::post('haulers/{client}/toggle', [\App\Http\Controllers\Biuro\HaulerController::class, 'toggle'])->name('haulers.toggle');

        Route::post('fractions/{fraction}/toggle', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'toggle'])->name('fractions.toggle');
        Route::get('fractions', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'index'])->name('fractions.index');
        Route::post('fractions', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'store'])->name('fractions.store');
        Route::resource('fractions', \App\Http\Controllers\Biuro\WasteFractionController::class)->only(['update']);

        // Ważenia
        Route::get('weighings', [\App\Http\Controllers\Biuro\WeighingController::class, 'index'])->name('weighings.index');
        Route::get('weighings/archived', [\App\Http\Controllers\Biuro\WeighingController::class, 'archived'])->name('weighings.archived');
        Route::post('weighings', [\App\Http\Controllers\Biuro\WeighingController::class, 'store'])->name('weighings.store');
        Route::get('weighings/{weighing}/edit', [\App\Http\Controllers\Biuro\WeighingController::class, 'edit'])->name('weighings.edit');
        Route::put('weighings/{weighing}', [\App\Http\Controllers\Biuro\WeighingController::class, 'update'])->name('weighings.update');
        Route::delete('weighings/{weighing}', [\App\Http\Controllers\Biuro\WeighingController::class, 'destroy'])->name('weighings.destroy');
        Route::post('weighings/{weighing}/archive', [\App\Http\Controllers\Biuro\WeighingController::class, 'archive'])->name('weighings.archive');
        Route::post('weighings/{weighing}/unarchive', [\App\Http\Controllers\Biuro\WeighingController::class, 'unarchive'])->name('weighings.unarchive');

        // Raporty
        Route::get('reports/loadings', [\App\Http\Controllers\Biuro\ReportController::class, 'loadings'])->name('reports.loadings');
        Route::get('reports/loadings/archived', [\App\Http\Controllers\Biuro\ReportController::class, 'loadingsArchived'])->name('reports.loadings.archived');
        Route::post('reports/loadings/{order}/revert', [\App\Http\Controllers\Biuro\ReportController::class, 'revert'])->name('reports.loadings.revert');
        Route::post('reports/loadings/{order}/archive', [\App\Http\Controllers\Biuro\ReportController::class, 'archive'])->name('reports.loadings.archive');
        Route::get('reports/deliveries', [\App\Http\Controllers\Biuro\ReportController::class, 'deliveries'])->name('reports.deliveries');
        Route::post('reports/deliveries/{order}/revert', [\App\Http\Controllers\Biuro\ReportController::class, 'revertDelivery'])->name('reports.deliveries.revert');
        Route::post('reports/deliveries/{order}/archive', [\App\Http\Controllers\Biuro\ReportController::class, 'archiveDelivery'])->name('reports.deliveries.archive');
        Route::get('reports/deliveries/archived', [\App\Http\Controllers\Biuro\ReportController::class, 'deliveriesArchived'])->name('reports.deliveries.archived');
        Route::post('reports/deliveries/{order}/unarchive', [\App\Http\Controllers\Biuro\ReportController::class, 'unarchiveDelivery'])->name('reports.deliveries.unarchive');

        Route::get('reports/weighings', [\App\Http\Controllers\Biuro\ReportController::class, 'weighings'])->name('reports.weighings');
        Route::post('reports/weighings/{order}/revert', [\App\Http\Controllers\Biuro\ReportController::class, 'revertWeighing'])->name('reports.weighings.revert');
        Route::post('reports/weighings/{order}/delete', [\App\Http\Controllers\Biuro\ReportController::class, 'deleteWeighing'])->name('reports.weighings.delete');

        Route::post('reports/loadings/{order}/unarchive', [\App\Http\Controllers\Biuro\ReportController::class, 'unarchive'])->name('reports.loadings.unarchive');

        // Pojazdy
        Route::resource('vehicles', \App\Http\Controllers\Biuro\VehicleController::class)->only(['index', 'store', 'update', 'destroy']);

        // Kontrahenci
        Route::get('clients/gus', [\App\Http\Controllers\Biuro\ClientController::class, 'gusLookup'])->name('clients.gus');
        Route::get('clients/data', [\App\Http\Controllers\Biuro\ClientController::class, 'data'])->name('clients.data');
        Route::resource('clients', \App\Http\Controllers\Biuro\ClientController::class);
        Route::post('clients/{client}/contacts', [\App\Http\Controllers\Biuro\ClientController::class, 'storeContact'])
             ->name('clients.contacts.store');
        Route::delete('clients/{client}/contacts/{contact}', [\App\Http\Controllers\Biuro\ClientController::class, 'destroyContact'])
             ->name('clients.contacts.destroy');
        Route::post('clients/{client}/addresses', [\App\Http\Controllers\Biuro\ClientController::class, 'storeAddress'])
             ->name('clients.addresses.store');
        Route::put('clients/{client}/addresses/{address}', [\App\Http\Controllers\Biuro\ClientController::class, 'updateAddress'])
             ->name('clients.addresses.update');
        Route::delete('clients/{client}/addresses/{address}', [\App\Http\Controllers\Biuro\ClientController::class, 'destroyAddress'])
             ->name('clients.addresses.destroy');

        // Raport wysyłek
        // Plan na plac
        Route::get('plan-na-plac', [\App\Http\Controllers\Biuro\PlanningController::class, 'planNaPlac'])->name('plan-na-plac');
        Route::post('orders/{order}/plac-date', [\App\Http\Controllers\Biuro\OrderController::class, 'setPlacDate'])->name('orders.plac-date');

        Route::get('raporty/wysylki', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'index'])->name('raporty.wysylki');
        Route::post('raporty/wysylki/cena/{order}', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveCena'])->name('raporty.wysylki.cena');
        Route::post('raporty/wysylki/cena-bulk', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveCenaBulk'])->name('raporty.wysylki.cena-bulk');
        Route::post('raporty/wysylki/transport/{order}', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveTransport'])->name('raporty.wysylki.transport');

        // Koszty transportu - ustawienia
        Route::get('koszty-transportu', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'index'])->name('koszty-transportu.index');
        Route::post('koszty-transportu', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'store'])->name('koszty-transportu.store');
        Route::put('koszty-transportu/{kosztTransportu}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'update'])->name('koszty-transportu.update');
        Route::delete('koszty-transportu/{kosztTransportu}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'destroy'])->name('koszty-transportu.destroy');

        // Przewoźnicy
        Route::post('przewoznicy', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'storePrzewoznik'])->name('przewoznicy.store');
        Route::put('przewoznicy/{przewoznik}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'updatePrzewoznik'])->name('przewoznicy.update');
        Route::delete('przewoznicy/{przewoznik}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'destroyPrzewoznik'])->name('przewoznicy.destroy');

        // Reklamacje
        Route::get('reklamacje',            [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'index'])->name('reklamacje.index');
        Route::get('reklamacje/bledy',      [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'bledy'])->name('reklamacje.bledy');
        Route::patch('reklamacje/bledy/{reklamacjaBled}', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'bladUpdate'])->name('reklamacje.bledy.update');
        Route::post('reklamacje/fetch-mail',[\App\Http\Controllers\Biuro\ReklamacjeController::class, 'fetchMail'])->name('reklamacje.fetch-mail');
        Route::get('reklamacje/plik/{path}', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'showFile'])->name('reklamacje.plik')->where('path', '.*');

        // Annex 7 - dokumenty
        Route::prefix('annex7')->name('annex7.')->group(function () {
            Route::get('/contractor/{contractor}', [\App\Http\Controllers\Biuro\Annex7Controller::class, 'contractorData'])->name('contractor-data');
            Route::get('/',             [\App\Http\Controllers\Biuro\Annex7Controller::class, 'index'])->name('index');
            Route::get('/create',       [\App\Http\Controllers\Biuro\Annex7Controller::class, 'create'])->name('create');
            Route::post('/',            [\App\Http\Controllers\Biuro\Annex7Controller::class, 'store'])->name('store');
            Route::get('/{annex7}',     [\App\Http\Controllers\Biuro\Annex7Controller::class, 'show'])->name('show');
            Route::get('/{annex7}/pdf', [\App\Http\Controllers\Biuro\Annex7Controller::class, 'generatePdf'])->name('pdf');
        });
    });

// ── KIEROWCA ──────────────────────────────────────────────────────────────────

Route::prefix('kierowca')
    ->middleware(['auth', 'module:kierowca'])
    ->name('kierowca.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Kierowca\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Kierowca\DashboardController::class, 'index'])->name('index');
        Route::post('orders/{order}/status', [\App\Http\Controllers\Kierowca\DashboardController::class, 'setStatus'])->name('orders.status');
        Route::get('orders/{order}/weigh', [\App\Http\Controllers\Kierowca\DashboardController::class, 'weighForm'])->name('orders.weigh');
        Route::post('orders/{order}/weigh', [\App\Http\Controllers\Kierowca\DashboardController::class, 'weighSave'])->name('orders.weighSave');
        Route::post('orders/{order}/weigh-confirm', [\App\Http\Controllers\Kierowca\DashboardController::class, 'weighConfirm'])->name('orders.weighConfirm');
        Route::post('orders/{order}/receiver-weight', [\App\Http\Controllers\Kierowca\DashboardController::class, 'saveReceiverWeight'])->name('orders.receiverWeight');
    });

// ── HAKOWIEC ──────────────────────────────────────────────────────────────────

Route::prefix('hakowiec')
    ->middleware(['auth', 'module:hakowiec'])
    ->name('hakowiec.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('hakowiec.dashboard'))->name('dashboard');
    });

// ── PLAC ──────────────────────────────────────────────────────────────────────

Route::prefix('plac')
    ->middleware(['auth', 'module:plac'])
    ->name('plac.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Plac\DashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Plac\DashboardController::class, 'index'])->name('dashboard');

        // Plan dnia
        Route::get('orders', [\App\Http\Controllers\Plac\DashboardController::class, 'orders'])->name('orders');
        Route::get('orders/{order}/loading', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingForm'])->name('orders.loading');
        Route::get('orders/{order}/loading/add', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingAdd'])->name('orders.loading.add');
        Route::get('orders/{order}/loading/{item}/edit', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingEdit'])->name('orders.loading.edit');
        Route::post('orders/{order}/loading', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingStore'])->name('orders.loading.store');
        Route::delete('orders/{order}/loading/{item}', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingDestroy'])->name('orders.loading.destroy');
        Route::post('orders/{order}/close', [\App\Http\Controllers\Plac\DashboardController::class, 'closeLoading'])->name('orders.close');

        // Załadunki
        Route::get('loading', [\App\Http\Controllers\Plac\LoadingController::class, 'index'])->name('loading.index');

        // Produkcja
        Route::get('production', [\App\Http\Controllers\Plac\ProductionController::class, 'index'])->name('production.index');
        Route::get('production/create', [\App\Http\Controllers\Plac\ProductionController::class, 'create'])->name('production.create');
        Route::post('production', [\App\Http\Controllers\Plac\ProductionController::class, 'store'])->name('production.store');
        Route::delete('production/{item}', [\App\Http\Controllers\Plac\ProductionController::class, 'destroy'])->name('production.destroy');

        // Przyjęcie towaru (dostawy)
        Route::get('delivery', [\App\Http\Controllers\Plac\DeliveryController::class, 'index'])->name('delivery.index');
        Route::get('delivery/{order}/form', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryForm'])->name('delivery.form');
        Route::get('delivery/{order}/add', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryAdd'])->name('delivery.add');
        Route::get('delivery/{order}/items/{item}/edit', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryEdit'])->name('delivery.edit');
        Route::post('delivery/{order}/items', [\App\Http\Controllers\Plac\DeliveryController::class, 'store'])->name('delivery.store');
        Route::delete('delivery/{order}/items/{item}', [\App\Http\Controllers\Plac\DeliveryController::class, 'destroy'])->name('delivery.destroy');
        Route::post('delivery/{order}/close', [\App\Http\Controllers\Plac\DeliveryController::class, 'close'])->name('delivery.close');

        // Magazyn
        Route::get('warehouse', [\App\Http\Controllers\Plac\WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('warehouse/{fraction}/history', [\App\Http\Controllers\Plac\WarehouseController::class, 'history'])->name('warehouse.history');

        // Inwentaryzacja
        // Paliwo
        Route::get('fuel', [\App\Http\Controllers\Plac\FuelController::class, 'index'])->name('fuel.index');
        Route::post('fuel', [\App\Http\Controllers\Plac\FuelController::class, 'store'])->name('fuel.store');
        Route::delete('fuel/{transaction}', [\App\Http\Controllers\Plac\FuelController::class, 'destroy'])->name('fuel.destroy');

        Route::get('inventory', [\App\Http\Controllers\Plac\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{fraction}/adjust', [\App\Http\Controllers\Plac\InventoryController::class, 'adjust'])->name('inventory.adjust');

        Route::get('stock', [\App\Http\Controllers\Plac\DashboardController::class, 'stock'])->name('stock');
    });

// ── HANDLOWIEC ────────────────────────────────────────────────────────────────

Route::prefix('handlowiec')
    ->middleware(['auth', 'module:handlowiec'])
    ->name('handlowiec.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('handlowiec.dashboard'))->name('dashboard');
    });

