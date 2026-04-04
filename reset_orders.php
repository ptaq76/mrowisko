<?php

// reset_orders.php
// Uruchom: php reset_orders.php
// UWAGA: Usuwa wszystkie zlecenia i powiązane dane!

$host   = '127.0.0.1';
$db     = 'mrowisko_local';
$user   = 'root';
$pass   = '';
$port   = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $tables = [
        'loading_items',
        'warehouse_items',   // wpisy załadunków (origin=loading)
        'order_containers',
        'pickup_requests',
        'visits',
        'bdo_cards',
        'orders',
    ];

    foreach ($tables as $table) {
        // Sprawdź czy tabela istnieje
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() === 0) {
            echo "  Pominięto (brak tabeli): $table\n";
            continue;
        }

        if ($table === 'warehouse_items') {
            // Usuń tylko wpisy z załadunków, nie produkcję
            $count = $pdo->exec("DELETE FROM warehouse_items WHERE origin = 'loading'");
        } else {
            $count = $pdo->exec("TRUNCATE TABLE $table");
        }

        echo "  Wyczyszczono: $table ($count wierszy)\n";
    }

    // Zresetuj auto_increment dla orders
    $pdo->exec('ALTER TABLE orders AUTO_INCREMENT = 1');

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    echo "\nGotowe! Wszystkie zlecenia zostały usunięte.\n";

} catch (PDOException $e) {
    echo "Błąd: " . $e->getMessage() . "\n";
}
