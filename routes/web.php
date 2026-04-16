<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Biuro\BdoController;

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

        Route::resource('annex7-contractors',         \App\Http\Controllers\Admin\Annex7ContractorController::class)->names('annex7-contractors');
        Route::resource('annex7-recovery-operations', \App\Http\Controllers\Admin\Annex7RecoveryOperationController::class)->names('annex7-recovery-operations');
        Route::resource('annex7-waste-descriptions',  \App\Http\Controllers\Admin\Annex7WasteDescriptionController::class)->names('annex7-waste-descriptions');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::patch('users/{user}/password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
             ->name('users.password');
    });

    // ── GUS – dostępne dla wszystkich zalogowanych ────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/gus', [\App\Http\Controllers\Biuro\ClientController::class, 'gusLookup'])->name('gus.lookup');
});

// ── BIURO ─────────────────────────────────────────────────────────────────────

Route::prefix('biuro')
    ->middleware(['auth', 'module:biuro'])
    ->name('biuro.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('biuro.dashboard'))->name('dashboard');
        Route::get('ustawienia', fn() => redirect()->route('biuro.fractions.index'))->name('ustawienia');

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
        Route::post('fractions/{fraction}/toggle', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'toggle'])->name('fractions.toggle');
        Route::get('fractions', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'index'])->name('fractions.index');
        Route::post('fractions', [\App\Http\Controllers\Biuro\WasteFractionController::class, 'store'])->name('fractions.store');
        Route::resource('fractions', \App\Http\Controllers\Biuro\WasteFractionController::class)->only(['update']);

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

        // Ważenia
        Route::get('weighings/tare-for-vehicles', [\App\Http\Controllers\Biuro\WeighingController::class, 'tareForVehicles'])->name('weighings.tare-for-vehicles');
        Route::get('weighings/all-tares',         [\App\Http\Controllers\Biuro\WeighingController::class, 'allTares'])->name('weighings.all-tares');
        Route::get('weighings/tare-for-vehicles', [\App\Http\Controllers\Biuro\WeighingController::class, 'tareForVehicles'])->name('weighings.tare-for-vehicles');
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
        Route::post('reports/loadings/{order}/unarchive', [\App\Http\Controllers\Biuro\ReportController::class, 'unarchive'])->name('reports.loadings.unarchive');
        Route::get('reports/deliveries', [\App\Http\Controllers\Biuro\ReportController::class, 'deliveries'])->name('reports.deliveries');
        Route::post('reports/deliveries/{order}/revert', [\App\Http\Controllers\Biuro\ReportController::class, 'revertDelivery'])->name('reports.deliveries.revert');
        Route::post('reports/deliveries/{order}/archive', [\App\Http\Controllers\Biuro\ReportController::class, 'archiveDelivery'])->name('reports.deliveries.archive');
        Route::get('reports/deliveries/archived', [\App\Http\Controllers\Biuro\ReportController::class, 'deliveriesArchived'])->name('reports.deliveries.archived');
        Route::post('reports/deliveries/{order}/unarchive', [\App\Http\Controllers\Biuro\ReportController::class, 'unarchiveDelivery'])->name('reports.deliveries.unarchive');
        Route::get('reports/warehouse', [\App\Http\Controllers\Biuro\WarehouseController::class, 'index'])->name('reports.warehouse');
        Route::get('reports/warehouse/{fractionId}/history', [\App\Http\Controllers\Biuro\WarehouseController::class, 'history'])->name('reports.warehouse.history');
        Route::get('reports/weighings', [\App\Http\Controllers\Biuro\ReportController::class, 'weighings'])->name('reports.weighings');
        Route::post('reports/weighings/{order}/revert', [\App\Http\Controllers\Biuro\ReportController::class, 'revertWeighing'])->name('reports.weighings.revert');
        Route::post('reports/weighings/{order}/delete', [\App\Http\Controllers\Biuro\ReportController::class, 'deleteWeighing'])->name('reports.weighings.delete');
        Route::get('reports/pickup-requests', [\App\Http\Controllers\Biuro\ReportController::class, 'pickupRequests'])->name('reports.pickup-requests');

        // Pojazdy
        Route::resource('vehicles', \App\Http\Controllers\Biuro\VehicleController::class)->only(['index', 'store', 'update', 'destroy']);

        // Kontrahenci
        Route::get('clients/gus', [\App\Http\Controllers\Biuro\ClientController::class, 'gusLookup'])->name('clients.gus');
        Route::get('clients/data', [\App\Http\Controllers\Biuro\ClientController::class, 'data'])->name('clients.data');
        Route::resource('clients', \App\Http\Controllers\Biuro\ClientController::class);
        Route::post('clients/{client}/contacts', [\App\Http\Controllers\Biuro\ClientController::class, 'storeContact'])->name('clients.contacts.store');
        Route::delete('clients/{client}/contacts/{contact}', [\App\Http\Controllers\Biuro\ClientController::class, 'destroyContact'])->name('clients.contacts.destroy');
        Route::post('clients/{client}/addresses', [\App\Http\Controllers\Biuro\ClientController::class, 'storeAddress'])->name('clients.addresses.store');
        Route::put('clients/{client}/addresses/{address}', [\App\Http\Controllers\Biuro\ClientController::class, 'updateAddress'])->name('clients.addresses.update');
        Route::delete('clients/{client}/addresses/{address}', [\App\Http\Controllers\Biuro\ClientController::class, 'destroyAddress'])->name('clients.addresses.destroy');

        // Plan na plac
        Route::get('plan-na-plac', [\App\Http\Controllers\Biuro\PlanningController::class, 'planNaPlac'])->name('plan-na-plac');
        Route::post('orders/{order}/plac-date', [\App\Http\Controllers\Biuro\OrderController::class, 'setPlacDate'])->name('orders.plac-date');

        // Raport wysyłek
        Route::get('raporty/wysylki', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'index'])->name('raporty.wysylki');
        Route::post('raporty/wysylki/cena/{order}', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveCena'])->name('raporty.wysylki.cena');
        Route::post('raporty/wysylki/cena-bulk', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveCenaBulk'])->name('raporty.wysylki.cena-bulk');
        Route::post('raporty/wysylki/transport/{order}', [\App\Http\Controllers\Biuro\RaportWysylekController::class, 'saveTransport'])->name('raporty.wysylki.transport');

        // Koszty transportu
        Route::get('koszty-transportu', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'index'])->name('koszty-transportu.index');
        Route::post('koszty-transportu', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'store'])->name('koszty-transportu.store');
        Route::put('koszty-transportu/{kosztTransportu}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'update'])->name('koszty-transportu.update');
        Route::delete('koszty-transportu/{kosztTransportu}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'destroy'])->name('koszty-transportu.destroy');

        // Przewoźnicy
        Route::post('przewoznicy', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'storePrzewoznik'])->name('przewoznicy.store');
        Route::put('przewoznicy/{przewoznik}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'updatePrzewoznik'])->name('przewoznicy.update');
        Route::delete('przewoznicy/{przewoznik}', [\App\Http\Controllers\Biuro\KosztTransportuController::class, 'destroyPrzewoznik'])->name('przewoznicy.destroy');

        // Reklamacje
        Route::get('reklamacje', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'index'])->name('reklamacje.index');
        Route::get('reklamacje/bledy', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'bledy'])->name('reklamacje.bledy');
        Route::patch('reklamacje/bledy/{reklamacjaBled}', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'bladUpdate'])->name('reklamacje.bledy.update');
        Route::post('reklamacje/fetch-mail', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'fetchMail'])->name('reklamacje.fetch-mail');
        Route::get('reklamacje/plik/{path}', [\App\Http\Controllers\Biuro\ReklamacjeController::class, 'showFile'])->name('reklamacje.plik')->where('path', '.*');

        //Tary pojazdów
        Route::get('vehicle-sets',              [\App\Http\Controllers\Biuro\VehicleSetController::class, 'index'])->name('vehicle-sets.index');
        Route::post('vehicle-sets',             [\App\Http\Controllers\Biuro\VehicleSetController::class, 'store'])->name('vehicle-sets.store');
        Route::put('vehicle-sets/{vehicleSet}', [\App\Http\Controllers\Biuro\VehicleSetController::class, 'update'])->name('vehicle-sets.update');
        Route::delete('vehicle-sets/{vehicleSet}', [\App\Http\Controllers\Biuro\VehicleSetController::class, 'destroy'])->name('vehicle-sets.destroy');

        // Pojazdy terminy
        Route::get('pojazdy-terminy',                    [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'index'])->name('pojazdy-terminy.index');
        Route::post('pojazdy-terminy',                   [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'store'])->name('pojazdy-terminy.store');
        Route::put('pojazdy-terminy/{akcja}',            [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'update'])->name('pojazdy-terminy.update');
        Route::delete('pojazdy-terminy/{akcja}',         [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'destroy'])->name('pojazdy-terminy.destroy');
        Route::post('pojazdy-terminy/pojazdy',               [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'storePojazd'])->name('pojazdy-terminy.pojazdy.store');
        Route::put('pojazdy-terminy/pojazdy/{pojazd}',        [\App\Http\Controllers\Biuro\PojazdyTerminyController::class, 'updatePojazd'])->name('pojazdy-terminy.pojazdy.update');
// BDO - Karty odpadów
// ══════════════════════════════════════════════════════════════════════════════

// Widoki
Route::get('bdo/karty', [BdoController::class, 'index'])->name('bdo.karty');
Route::get('bdo/karty-przekazujacy', [BdoController::class, 'indexPrzekazujacy'])->name('bdo.kartyPrzekazujacy');

// Akcje na kartach
Route::post('bdo/potwierdz-karte', [BdoController::class, 'potwierdzKarte'])->name('bdo.potwierdzKarte');
Route::post('bdo/odrzuc-karte', [BdoController::class, 'odrzucKarte'])->name('bdo.odrzucKarte');
Route::post('bdo/aktualizuj-karte', [BdoController::class, 'aktualizujJednaKarte'])->name('bdo.aktualizujKarte');
Route::post('bdo/potwierdz-rozpoczecie', [BdoController::class, 'potwierdzRozpoczecie'])->name('bdo.potwierdzRozpoczecie');

// Synchronizacja
Route::post('bdo/sync', [BdoController::class, 'sync'])->name('bdo.sync');
Route::post('bdo/sync-przekazujacy', [BdoController::class, 'syncPrzekazujacy'])->name('bdo.syncPrzekazujacy');

        // Annex 7
        Route::prefix('annex7')->name('annex7.')->group(function () {
            Route::get('/contractor/{contractor}', [\App\Http\Controllers\Biuro\Annex7Controller::class, 'contractorData'])->name('contractor-data');
            Route::get('/',             [\App\Http\Controllers\Biuro\Annex7Controller::class, 'index'])->name('index');
            Route::get('/create',       [\App\Http\Controllers\Biuro\Annex7Controller::class, 'create'])->name('create');
            Route::post('/',            [\App\Http\Controllers\Biuro\Annex7Controller::class, 'store'])->name('store');
            Route::get('/{annex7}',     [\App\Http\Controllers\Biuro\Annex7Controller::class, 'show'])->name('show');
            Route::get('/{annex7}/pdf', [\App\Http\Controllers\Biuro\Annex7Controller::class, 'generatePdf'])->name('pdf');
        });

        // Zlecenia handlowców – odrzucenie
        Route::post('pickup-requests/{pickupRequest}/odrzuc', [\App\Http\Controllers\Biuro\OrderController::class, 'odrzucPickupRequest'])->name('pickup-requests.odrzuc');
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
        Route::post('orders/{order}/weigh-confirm-hakowiec', [\App\Http\Controllers\Kierowca\DashboardController::class, 'weighConfirmHakowiec'])->name('orders.weighConfirmHakowiec');
        Route::post('orders/{order}/receiver-weight', [\App\Http\Controllers\Kierowca\DashboardController::class, 'saveReceiverWeight'])->name('orders.receiverWeight');
        Route::get('/kursy', [\App\Http\Controllers\Kierowca\DashboardController::class, 'kursy'])->name('kursy');
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

        Route::get('orders', [\App\Http\Controllers\Plac\DashboardController::class, 'orders'])->name('orders');
        Route::get('orders/{order}/loading', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingForm'])->name('orders.loading');
        Route::get('orders/{order}/loading/add', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingAdd'])->name('orders.loading.add');
        Route::get('orders/{order}/loading/{item}/edit', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingEdit'])->name('orders.loading.edit');
        Route::post('orders/{order}/loading', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingStore'])->name('orders.loading.store');
        Route::delete('orders/{order}/loading/{item}', [\App\Http\Controllers\Plac\DashboardController::class, 'loadingDestroy'])->name('orders.loading.destroy');
        Route::post('orders/{order}/close', [\App\Http\Controllers\Plac\DashboardController::class, 'closeLoading'])->name('orders.close');

        Route::get('loading', [\App\Http\Controllers\Plac\LoadingController::class, 'index'])->name('loading.index');

        Route::get('production', [\App\Http\Controllers\Plac\ProductionController::class, 'index'])->name('production.index');
        Route::get('production/create', [\App\Http\Controllers\Plac\ProductionController::class, 'create'])->name('production.create');
        Route::post('production', [\App\Http\Controllers\Plac\ProductionController::class, 'store'])->name('production.store');
        Route::delete('production/{item}', [\App\Http\Controllers\Plac\ProductionController::class, 'destroy'])->name('production.destroy');

        Route::get('delivery', [\App\Http\Controllers\Plac\DeliveryController::class, 'index'])->name('delivery.index');
        Route::get('delivery/{order}/form', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryForm'])->name('delivery.form');
        Route::get('delivery/{order}/add', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryAdd'])->name('delivery.add');
        Route::get('delivery/{order}/items/{item}/edit', [\App\Http\Controllers\Plac\DeliveryController::class, 'deliveryEdit'])->name('delivery.edit');
        Route::post('delivery/{order}/items', [\App\Http\Controllers\Plac\DeliveryController::class, 'store'])->name('delivery.store');
        Route::delete('delivery/{order}/items/{item}', [\App\Http\Controllers\Plac\DeliveryController::class, 'destroy'])->name('delivery.destroy');
        Route::post('delivery/{order}/close', [\App\Http\Controllers\Plac\DeliveryController::class, 'close'])->name('delivery.close');

        Route::get('warehouse', [\App\Http\Controllers\Plac\WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('warehouse/{fraction}/history', [\App\Http\Controllers\Plac\WarehouseController::class, 'history'])->name('warehouse.history');

        Route::get('fuel', [\App\Http\Controllers\Plac\FuelController::class, 'index'])->name('fuel.index');
        Route::post('fuel', [\App\Http\Controllers\Plac\FuelController::class, 'store'])->name('fuel.store');
        Route::delete('fuel/{transaction}', [\App\Http\Controllers\Plac\FuelController::class, 'destroy'])->name('fuel.destroy');

        Route::get('inventory', [\App\Http\Controllers\Plac\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{fraction}/adjust', [\App\Http\Controllers\Plac\InventoryController::class, 'adjust'])->name('inventory.adjust');

        Route::get('stock', [\App\Http\Controllers\Plac\DashboardController::class, 'stock'])->name('stock');
    });

// ── HANDLOWIEC ────────────────────────────────────────────────────────────────

Route::prefix('handlowiec')
    ->name('handlowiec.')
    ->middleware(['auth', 'module:handlowiec'])
    ->group(function () {
        Route::get('/dashboard',                         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/nowe-zlecenie',                     [\App\Http\Controllers\Handlowiec\DashboardController::class, 'noweZlecenie'])->name('nowe-zlecenie');
        Route::post('/zlecenia',                         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'store'])->name('zlecenia.store');
        Route::get('/zlecenia',                          [\App\Http\Controllers\Handlowiec\DashboardController::class, 'zlecenia'])->name('zlecenia');
        Route::get('/historia-klienta/{client}',         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'historiaKlienta'])->name('historia-klienta');

        // Klienci
        Route::get('/klienci',                           [\App\Http\Controllers\Handlowiec\DashboardController::class, 'klienci'])->name('klienci');
        Route::get('/nowy-klient',                       [\App\Http\Controllers\Handlowiec\DashboardController::class, 'nowyKlient'])->name('nowy-klient');
        Route::post('/nowy-klient',                      [\App\Http\Controllers\Handlowiec\DashboardController::class, 'storeKlient'])->name('klient-store');
        Route::get('/check-nip',                         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'checkNip'])->name('check-nip');
        Route::get('/klienci/{client}/edit',             [\App\Http\Controllers\Handlowiec\DashboardController::class, 'klientEdit'])->name('klient-edit');
        Route::post('/klienci/{client}/update',          [\App\Http\Controllers\Handlowiec\DashboardController::class, 'klientUpdate'])->name('klient-update');

        // Adresy
        Route::post('/klienci/{client}/addresses',                          [\App\Http\Controllers\Handlowiec\DashboardController::class, 'storeAddress'])->name('klient-address-store');
        Route::post('/klienci/{client}/addresses/{address}/update',         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'updateAddress'])->name('klient-address-update');
        Route::post('/klienci/{client}/addresses/{address}/delete',         [\App\Http\Controllers\Handlowiec\DashboardController::class, 'destroyAddress'])->name('klient-address-destroy');

        // Kontakty
        Route::post('/klienci/{client}/contacts',                           [\App\Http\Controllers\Handlowiec\DashboardController::class, 'storeContact'])->name('klient-contact-store');
        Route::post('/klienci/{client}/contacts/{contact}/delete',          [\App\Http\Controllers\Handlowiec\DashboardController::class, 'destroyContact'])->name('klient-contact-destroy');
    });