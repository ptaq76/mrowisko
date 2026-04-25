<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Annex7ContractorController;
use App\Http\Controllers\Admin\Annex7RecoveryOperationController;
use App\Http\Controllers\Admin\Annex7WasteDescriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Biuro\Annex7Controller;
use App\Http\Controllers\Biuro\BdoController;
use App\Http\Controllers\Biuro\ClientController;
use App\Http\Controllers\Biuro\FuelVehicleController;
use App\Http\Controllers\Biuro\HaulerController;
use App\Http\Controllers\Biuro\ImporterController;
use App\Http\Controllers\Biuro\KosztTransportuController;
use App\Http\Controllers\Biuro\LieferscheinController;
use App\Http\Controllers\Biuro\OrderController;
use App\Http\Controllers\Biuro\PlanningController;
use App\Http\Controllers\Biuro\PojazdyTerminyController;
use App\Http\Controllers\Biuro\RaportWysylekController;
use App\Http\Controllers\Biuro\ReklamacjeController;
use App\Http\Controllers\Biuro\ReportController;
use App\Http\Controllers\Biuro\VehicleController;
use App\Http\Controllers\Biuro\VehicleSetController;
use App\Http\Controllers\Biuro\WarehouseController;
use App\Http\Controllers\Biuro\WasteCodeController;
use App\Http\Controllers\Biuro\WasteFractionController;
use App\Http\Controllers\Biuro\WeighingController;
use App\Http\Controllers\Kierowca\DashboardController;
use App\Http\Controllers\Plac\DeliveryController;
use App\Http\Controllers\Plac\FuelController;
use App\Http\Controllers\Plac\InventoryController;
use App\Http\Controllers\Plac\LoadingController;
use App\Http\Controllers\Plac\ProductionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Biuro\OpakowaniaController;

// ── AUTH ──────────────────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', fn () => redirect()->route('login'));

// ── ADMIN ─────────────────────────────────────────────────────────────────────

Route::prefix('admin')
    ->middleware(['auth', 'module:admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/drivers', [AdminController::class, 'driversIndex'])->name('drivers.index');
        Route::get('/drivers/{driver}', [AdminController::class, 'driversShow'])->name('drivers.show');
        Route::get('/agent', [AdminController::class, 'agentView'])->name('agent');
        Route::post('/agent/chat', [AdminController::class, 'agentChat'])->name('agent.chat');
        Route::post('/agent/save', [AdminController::class, 'agentChatSave'])->name('agent.save');
        Route::delete('/agent/{chat}', [AdminController::class, 'agentChatDelete'])->name('agent.delete');

        Route::resource('annex7-contractors', Annex7ContractorController::class)->names('annex7-contractors');
        Route::resource('annex7-recovery-operations', Annex7RecoveryOperationController::class)->names('annex7-recovery-operations');
        Route::resource('annex7-waste-descriptions', Annex7WasteDescriptionController::class)->names('annex7-waste-descriptions');

        Route::resource('users', UserController::class);
        Route::patch('users/{user}/password', [UserController::class, 'resetPassword'])
            ->name('users.password');
    });

// ── GUS – dostępne dla wszystkich zalogowanych ────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/gus', [ClientController::class, 'gusLookup'])->name('gus.lookup');
});

// ── BIURO ─────────────────────────────────────────────────────────────────────

Route::prefix('biuro')
    ->middleware(['auth', 'module:biuro'])
    ->name('biuro.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('biuro.dashboard'))->name('dashboard');
        Route::get('ustawienia', fn () => redirect()->route('biuro.fractions.index'))->name('ustawienia');

        // Planowanie
        Route::get('planning', [PlanningController::class, 'index'])->name('planning.index');
        Route::get('orders/modal-data', [OrderController::class, 'modalData'])->name('orders.modalData');
        Route::get('orders/quick-buttons', [OrderController::class, 'quickButtons'])->name('orders.quickButtons');
        Route::post('orders/quick-buttons', [OrderController::class, 'quickButtonStore'])->name('orders.quickButtons.store');
        Route::put('orders/quick-buttons/{button}', [OrderController::class, 'quickButtonUpdate'])->name('orders.quickButtons.update');
        Route::delete('orders/quick-buttons/{button}', [OrderController::class, 'quickButtonDestroy'])->name('orders.quickButtons.destroy');
        Route::post('orders/{order}/status', [OrderController::class, 'setStatus'])->name('orders.status');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // Lieferschein
        Route::get('ls/pdf-files', [LieferscheinController::class, 'getPdfFiles'])->name('ls.pdfFiles');
        Route::get('ls/create', [LieferscheinController::class, 'create'])->name('ls.create');
        Route::get('ls', [LieferscheinController::class, 'index'])->name('ls.index');
        Route::post('ls/fetch-mail', [LieferscheinController::class, 'fetchFromMail'])->name('ls.fetch-mail');
        Route::post('ls', [LieferscheinController::class, 'store'])->name('ls.store');
        Route::get('ls/{lieferschein}/edit', [LieferscheinController::class, 'edit'])->name('ls.edit');
        Route::put('ls/{lieferschein}', [LieferscheinController::class, 'update'])->name('ls.update');
        Route::post('ls/{lieferschein}/status', [LieferscheinController::class, 'markStatus'])->name('ls.status');
        Route::delete('ls/{lieferschein}', [LieferscheinController::class, 'destroy'])->name('ls.destroy');
        Route::get('ls/{lieferschein}/pdf', [LieferscheinController::class, 'viewPdf'])->name('ls.pdf');

        // Importerzy
        Route::resource('importers', ImporterController::class)->only(['index', 'store', 'update']);

        // Frakcje odpadów
        Route::get('fractions/check-name', [WasteFractionController::class, 'checkName'])->name('fractions.checkName');
        Route::post('fractions/{fraction}/toggle', [WasteFractionController::class, 'toggle'])->name('fractions.toggle');
        Route::get('fractions', [WasteFractionController::class, 'index'])->name('fractions.index');
        Route::post('fractions', [WasteFractionController::class, 'store'])->name('fractions.store');
        Route::resource('fractions', WasteFractionController::class)->only(['update']);

        // Pojazdy paliwowe
        Route::get('fuel-vehicles', [FuelVehicleController::class, 'index'])->name('fuel-vehicles.index');
        Route::post('fuel-vehicles', [FuelVehicleController::class, 'store'])->name('fuel-vehicles.store');
        Route::put('fuel-vehicles/{fuelVehicle}', [FuelVehicleController::class, 'update'])->name('fuel-vehicles.update');
        Route::delete('fuel-vehicles/{fuelVehicle}', [FuelVehicleController::class, 'destroy'])->name('fuel-vehicles.destroy');
        Route::post('fuel-vehicles/{fuelVehicle}/toggle', [FuelVehicleController::class, 'toggle'])->name('fuel-vehicles.toggle');

        // Kody odpadów
        Route::get('waste-codes', [WasteCodeController::class, 'index'])->name('waste-codes.index');
        Route::post('waste-codes', [WasteCodeController::class, 'store'])->name('waste-codes.store');
        Route::put('waste-codes/{wasteCode}', [WasteCodeController::class, 'update'])->name('waste-codes.update');
        Route::delete('waste-codes/{wasteCode}', [WasteCodeController::class, 'destroy'])->name('waste-codes.destroy');

          // Opakowania
        Route::resource('opakowania', OpakowaniaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['opakowania' => 'opakowanie'])
            ->names([
                'index'   => 'opakowania.index',
                'store'   => 'opakowania.store',
                'update'  => 'opakowania.update',
                'destroy' => 'opakowania.destroy',
            ]);

        // Woźacy
        Route::get('haulers', [HaulerController::class, 'index'])->name('haulers.index');
        Route::post('haulers/{client}/toggle', [HaulerController::class, 'toggle'])->name('haulers.toggle');

        // Ważenia
        Route::get('weighings/tare-for-vehicles', [WeighingController::class, 'tareForVehicles'])->name('weighings.tare-for-vehicles');
        Route::get('weighings/all-tares', [WeighingController::class, 'allTares'])->name('weighings.all-tares');
        Route::get('weighings/tare-for-vehicles', [WeighingController::class, 'tareForVehicles'])->name('weighings.tare-for-vehicles');
        Route::get('weighings', [WeighingController::class, 'index'])->name('weighings.index');
        Route::get('weighings/archived', [WeighingController::class, 'archived'])->name('weighings.archived');
        Route::post('weighings', [WeighingController::class, 'store'])->name('weighings.store');
        Route::get('weighings/{weighing}/edit', [WeighingController::class, 'edit'])->name('weighings.edit');
        Route::put('weighings/{weighing}', [WeighingController::class, 'update'])->name('weighings.update');
        Route::delete('weighings/{weighing}', [WeighingController::class, 'destroy'])->name('weighings.destroy');
        Route::post('weighings/{weighing}/archive', [WeighingController::class, 'archive'])->name('weighings.archive');
        Route::post('weighings/{weighing}/unarchive', [WeighingController::class, 'unarchive'])->name('weighings.unarchive');

        // Raporty
        Route::get('reports/loadings', [ReportController::class, 'loadings'])->name('reports.loadings');
        Route::get('reports/loadings/archived', [ReportController::class, 'loadingsArchived'])->name('reports.loadings.archived');
        Route::post('reports/loadings/{order}/revert', [ReportController::class, 'revert'])->name('reports.loadings.revert');
        Route::post('reports/loadings/{order}/archive', [ReportController::class, 'archive'])->name('reports.loadings.archive');
        Route::post('reports/loadings/{order}/unarchive', [ReportController::class, 'unarchive'])->name('reports.loadings.unarchive');
        Route::get('reports/deliveries', [ReportController::class, 'deliveries'])->name('reports.deliveries');
        Route::post('reports/deliveries/{order}/revert', [ReportController::class, 'revertDelivery'])->name('reports.deliveries.revert');
        Route::post('reports/deliveries/{order}/archive', [ReportController::class, 'archiveDelivery'])->name('reports.deliveries.archive');
        Route::get('reports/deliveries/archived', [ReportController::class, 'deliveriesArchived'])->name('reports.deliveries.archived');
        Route::post('reports/deliveries/{order}/unarchive', [ReportController::class, 'unarchiveDelivery'])->name('reports.deliveries.unarchive');
        Route::get('reports/warehouse', [WarehouseController::class, 'index'])->name('reports.warehouse');
        Route::get('reports/warehouse/{fractionId}/history', [WarehouseController::class, 'history'])->name('reports.warehouse.history');
        Route::post('reports/warehouse/{fraction}/fav', [WarehouseController::class, 'toggleFav'])->name('reports.warehouse.fav');
        Route::get('reports/weighings', [ReportController::class, 'weighings'])->name('reports.weighings');
        Route::post('reports/weighings/{order}/revert', [ReportController::class, 'revertWeighing'])->name('reports.weighings.revert');
        Route::post('reports/weighings/{order}/delete', [ReportController::class, 'deleteWeighing'])->name('reports.weighings.delete');
        Route::get('reports/pickup-requests', [ReportController::class, 'pickupRequests'])->name('reports.pickup-requests');
        Route::get('reports/planning', [ReportController::class, 'planning'])->name('reports.planning');
        Route::get('reports/foreign-shipments', [ReportController::class, 'foreignShipments'])->name('reports.foreign-shipments');

        // Pojazdy
        Route::resource('vehicles', VehicleController::class)->only(['index', 'store', 'update', 'destroy']);

        // Kontrahenci
        Route::get('clients/gus', [ClientController::class, 'gusLookup'])->name('clients.gus');
        Route::get('clients/data', [ClientController::class, 'data'])->name('clients.data');
        Route::resource('clients', ClientController::class);
        Route::post('clients/{client}/contacts', [ClientController::class, 'storeContact'])->name('clients.contacts.store');
        Route::delete('clients/{client}/contacts/{contact}', [ClientController::class, 'destroyContact'])->name('clients.contacts.destroy');
        Route::post('clients/{client}/addresses', [ClientController::class, 'storeAddress'])->name('clients.addresses.store');
        Route::put('clients/{client}/addresses/{address}', [ClientController::class, 'updateAddress'])->name('clients.addresses.update');
        Route::delete('clients/{client}/addresses/{address}', [ClientController::class, 'destroyAddress'])->name('clients.addresses.destroy');

        // Plan na plac
        Route::get('plan-na-plac', [PlanningController::class, 'planNaPlac'])->name('plan-na-plac');
        Route::post('orders/{order}/plac-date', [OrderController::class, 'setPlacDate'])->name('orders.plac-date');

        // Raport wysyłek
        Route::get('raporty/wysylki', [RaportWysylekController::class, 'index'])->name('raporty.wysylki');
        Route::post('raporty/wysylki/cena/{order}', [RaportWysylekController::class, 'saveCena'])->name('raporty.wysylki.cena');
        Route::post('raporty/wysylki/cena-bulk', [RaportWysylekController::class, 'saveCenaBulk'])->name('raporty.wysylki.cena-bulk');
        Route::post('raporty/wysylki/transport/{order}', [RaportWysylekController::class, 'saveTransport'])->name('raporty.wysylki.transport');

        // Koszty transportu
        Route::get('koszty-transportu', [KosztTransportuController::class, 'index'])->name('koszty-transportu.index');
        Route::post('koszty-transportu', [KosztTransportuController::class, 'store'])->name('koszty-transportu.store');
        Route::put('koszty-transportu/{kosztTransportu}', [KosztTransportuController::class, 'update'])->name('koszty-transportu.update');
        Route::delete('koszty-transportu/{kosztTransportu}', [KosztTransportuController::class, 'destroy'])->name('koszty-transportu.destroy');

        // Przewoźnicy
        Route::post('przewoznicy', [KosztTransportuController::class, 'storePrzewoznik'])->name('przewoznicy.store');
        Route::put('przewoznicy/{przewoznik}', [KosztTransportuController::class, 'updatePrzewoznik'])->name('przewoznicy.update');
        Route::delete('przewoznicy/{przewoznik}', [KosztTransportuController::class, 'destroyPrzewoznik'])->name('przewoznicy.destroy');

        // Reklamacje
        Route::get('reklamacje', [ReklamacjeController::class, 'index'])->name('reklamacje.index');
        Route::get('reklamacje/bledy', [ReklamacjeController::class, 'bledy'])->name('reklamacje.bledy');
        Route::patch('reklamacje/bledy/{reklamacjaBled}', [ReklamacjeController::class, 'bladUpdate'])->name('reklamacje.bledy.update');
        Route::post('reklamacje/fetch-mail', [ReklamacjeController::class, 'fetchMail'])->name('reklamacje.fetch-mail');
        Route::get('reklamacje/plik/{path}', [ReklamacjeController::class, 'showFile'])->name('reklamacje.plik')->where('path', '.*');

        // Tary pojazdów
        Route::get('vehicle-sets', [VehicleSetController::class, 'index'])->name('vehicle-sets.index');
        Route::post('vehicle-sets', [VehicleSetController::class, 'store'])->name('vehicle-sets.store');
        Route::put('vehicle-sets/{vehicleSet}', [VehicleSetController::class, 'update'])->name('vehicle-sets.update');
        Route::delete('vehicle-sets/{vehicleSet}', [VehicleSetController::class, 'destroy'])->name('vehicle-sets.destroy');

        // Pojazdy terminy
        Route::get('pojazdy-terminy', [PojazdyTerminyController::class, 'index'])->name('pojazdy-terminy.index');
        Route::post('pojazdy-terminy', [PojazdyTerminyController::class, 'store'])->name('pojazdy-terminy.store');
        Route::put('pojazdy-terminy/{akcja}', [PojazdyTerminyController::class, 'update'])->name('pojazdy-terminy.update');
        Route::delete('pojazdy-terminy/{akcja}', [PojazdyTerminyController::class, 'destroy'])->name('pojazdy-terminy.destroy');
        Route::post('pojazdy-terminy/pojazdy', [PojazdyTerminyController::class, 'storePojazd'])->name('pojazdy-terminy.pojazdy.store');
        Route::put('pojazdy-terminy/pojazdy/{pojazd}', [PojazdyTerminyController::class, 'updatePojazd'])->name('pojazdy-terminy.pojazdy.update');
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
            Route::get('/contractor/{contractor}', [Annex7Controller::class, 'contractorData'])->name('contractor-data');
            Route::get('/', [Annex7Controller::class, 'index'])->name('index');
            Route::get('/create', [Annex7Controller::class, 'create'])->name('create');
            Route::post('/', [Annex7Controller::class, 'store'])->name('store');
            Route::get('/{annex7}', [Annex7Controller::class, 'show'])->name('show');
            Route::get('/{annex7}/pdf', [Annex7Controller::class, 'generatePdf'])->name('pdf');
        });

        // Zlecenia handlowców – odrzucenie
        Route::post('pickup-requests/{pickupRequest}/odrzuc', [OrderController::class, 'odrzucPickupRequest'])->name('pickup-requests.odrzuc');
    });

// ── KIEROWCA ──────────────────────────────────────────────────────────────────

Route::prefix('kierowca')
    ->middleware(['auth', 'module:kierowca'])
    ->name('kierowca.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::post('orders/{order}/status', [DashboardController::class, 'setStatus'])->name('orders.status');
        Route::get('orders/{order}/weigh', [DashboardController::class, 'weighForm'])->name('orders.weigh');
        Route::post('orders/{order}/weigh', [DashboardController::class, 'weighSave'])->name('orders.weighSave');
        Route::post('orders/{order}/weigh-confirm', [DashboardController::class, 'weighConfirm'])->name('orders.weighConfirm');
        Route::post('orders/{order}/weigh-confirm-hakowiec', [DashboardController::class, 'weighConfirmHakowiec'])->name('orders.weighConfirmHakowiec');
        Route::post('orders/{order}/receiver-weight', [DashboardController::class, 'saveReceiverWeight'])->name('orders.receiverWeight');
        Route::post('orders/{order}/packaging', [DashboardController::class, 'savePackaging'])->name('orders.packaging');
        Route::get('/kursy', [DashboardController::class, 'kursy'])->name('kursy');
    });

// ── HAKOWIEC ──────────────────────────────────────────────────────────────────

Route::prefix('hakowiec')
    ->middleware(['auth', 'module:hakowiec'])
    ->name('hakowiec.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('hakowiec.dashboard'))->name('dashboard');
    });

// ── PLAC ──────────────────────────────────────────────────────────────────────

Route::prefix('plac')
    ->middleware(['auth', 'module:plac'])
    ->name('plac.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Plac\DashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\Plac\DashboardController::class, 'index'])->name('dashboard');

        Route::get('orders', [App\Http\Controllers\Plac\DashboardController::class, 'orders'])->name('orders');
        Route::get('orders/{order}/loading', [App\Http\Controllers\Plac\DashboardController::class, 'loadingForm'])->name('orders.loading');
        Route::get('orders/{order}/loading/add', [App\Http\Controllers\Plac\DashboardController::class, 'loadingAdd'])->name('orders.loading.add');
        Route::get('orders/{order}/loading/{item}/edit', [App\Http\Controllers\Plac\DashboardController::class, 'loadingEdit'])->name('orders.loading.edit');
        Route::post('orders/{order}/loading', [App\Http\Controllers\Plac\DashboardController::class, 'loadingStore'])->name('orders.loading.store');
        Route::delete('orders/{order}/loading/{item}', [App\Http\Controllers\Plac\DashboardController::class, 'loadingDestroy'])->name('orders.loading.destroy');
        Route::post('orders/{order}/close', [App\Http\Controllers\Plac\DashboardController::class, 'closeLoading'])->name('orders.close');

        Route::get('loading', [LoadingController::class, 'index'])->name('loading.index');

        Route::get('production', [ProductionController::class, 'index'])->name('production.index');
        Route::get('production/create', [ProductionController::class, 'create'])->name('production.create');
        Route::post('production', [ProductionController::class, 'store'])->name('production.store');
        Route::delete('production/{item}', [ProductionController::class, 'destroy'])->name('production.destroy');

        Route::get('delivery', [DeliveryController::class, 'index'])->name('delivery.index');
        Route::get('delivery/{order}/form', [DeliveryController::class, 'deliveryForm'])->name('delivery.form');
        Route::get('delivery/{order}/add', [DeliveryController::class, 'deliveryAdd'])->name('delivery.add');
        Route::get('delivery/{order}/items/{item}/edit', [DeliveryController::class, 'deliveryEdit'])->name('delivery.edit');
        Route::post('delivery/{order}/items', [DeliveryController::class, 'store'])->name('delivery.store');
        Route::delete('delivery/{order}/items/{item}', [DeliveryController::class, 'destroy'])->name('delivery.destroy');
        Route::post('delivery/{order}/close', [DeliveryController::class, 'close'])->name('delivery.close');

        Route::get('warehouse', [App\Http\Controllers\Plac\WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('warehouse/{fraction}/history', [App\Http\Controllers\Plac\WarehouseController::class, 'history'])->name('warehouse.history');

        Route::get('fuel', [FuelController::class, 'index'])->name('fuel.index');
        Route::post('fuel', [FuelController::class, 'store'])->name('fuel.store');
        Route::delete('fuel/{transaction}', [FuelController::class, 'destroy'])->name('fuel.destroy');

        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{fraction}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

        Route::get('stock', [App\Http\Controllers\Plac\DashboardController::class, 'stock'])->name('stock');
            
        Route::post('delivery/{order}/close', [DeliveryController::class, 'close'])->name('delivery.close');
        Route::post('delivery/{order}/packaging/confirm', [DeliveryController::class, 'packagingConfirm'])->name('delivery.packaging.confirm');
        Route::post('delivery/{order}/packaging', [DeliveryController::class, 'packagingStore'])->name('delivery.packaging.store');
        Route::get('stock', [App\Http\Controllers\Plac\DashboardController::class, 'stock'])->name('stock');
        });

// ── HANDLOWIEC ────────────────────────────────────────────────────────────────

Route::prefix('handlowiec')
    ->name('handlowiec.')
    ->middleware(['auth', 'module:handlowiec'])
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Handlowiec\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/nowe-zlecenie', [App\Http\Controllers\Handlowiec\DashboardController::class, 'noweZlecenie'])->name('nowe-zlecenie');
        Route::post('/zlecenia', [App\Http\Controllers\Handlowiec\DashboardController::class, 'store'])->name('zlecenia.store');
        Route::get('/zlecenia', [App\Http\Controllers\Handlowiec\DashboardController::class, 'zlecenia'])->name('zlecenia');
        Route::get('/historia-klienta/{client}', [App\Http\Controllers\Handlowiec\DashboardController::class, 'historiaKlienta'])->name('historia-klienta');

        // Klienci
        Route::get('/klienci', [App\Http\Controllers\Handlowiec\DashboardController::class, 'klienci'])->name('klienci');
        Route::get('/nowy-klient', [App\Http\Controllers\Handlowiec\DashboardController::class, 'nowyKlient'])->name('nowy-klient');
        Route::post('/nowy-klient', [App\Http\Controllers\Handlowiec\DashboardController::class, 'storeKlient'])->name('klient-store');
        Route::get('/check-nip', [App\Http\Controllers\Handlowiec\DashboardController::class, 'checkNip'])->name('check-nip');
        Route::get('/klienci/{client}/edit', [App\Http\Controllers\Handlowiec\DashboardController::class, 'klientEdit'])->name('klient-edit');
        Route::post('/klienci/{client}/update', [App\Http\Controllers\Handlowiec\DashboardController::class, 'klientUpdate'])->name('klient-update');

        // Adresy
        Route::post('/klienci/{client}/addresses', [App\Http\Controllers\Handlowiec\DashboardController::class, 'storeAddress'])->name('klient-address-store');
        Route::post('/klienci/{client}/addresses/{address}/update', [App\Http\Controllers\Handlowiec\DashboardController::class, 'updateAddress'])->name('klient-address-update');
        Route::post('/klienci/{client}/addresses/{address}/delete', [App\Http\Controllers\Handlowiec\DashboardController::class, 'destroyAddress'])->name('klient-address-destroy');

        // Kontakty
        Route::post('/klienci/{client}/contacts', [App\Http\Controllers\Handlowiec\DashboardController::class, 'storeContact'])->name('klient-contact-store');
        Route::post('/klienci/{client}/contacts/{contact}/delete', [App\Http\Controllers\Handlowiec\DashboardController::class, 'destroyContact'])->name('klient-contact-destroy');
    });