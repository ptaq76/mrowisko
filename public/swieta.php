<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Warsaw');

// --- POLSKA ---
// Stałe święta PL
$fixedHolidaysPL = [
    '01-01' => 'Nowy Rok',
    '01-06' => 'Trzech Króli',
    '05-01' => 'Święto Pracy',
    '05-03' => 'Konstytucji 3 Maja',
    '08-15' => 'Wniebowzięcie NMP',
    '11-01' => 'Wszystkich Świętych',
    '11-11' => 'Święto Niepodległości',
    '12-24' => 'Wigilia',
    '12-25' => 'Boże Narodzenie',
    '12-26' => 'Drugi dzień Świąt',
    '07-16' => 'Urodziny Szefa',
];

// --- NIEMCY (landy przy PL) ---
// Stałe święta DE (ogólnokrajowe)
$fixedHolidaysDE = [
    '01-01' => 'Neujahr (Nowy Rok)',
    '05-01' => 'Tag der Arbeit (Święto Pracy)',
    '10-03' => 'Tag der Deutschen Einheit (Dzień Jedności)',
    '12-25' => '1. Weihnachtstag',
    '12-26' => '2. Weihnachtstag',
];

// Regionalne DE (np. Brandenburgia, Saksonia, Meklemburgia)
$regionalHolidaysDE = [
    '10-31' => 'Reformationstag',
];

// Zakres lat
$currentYear = intval(date('Y'));
$yearsRange = range($currentYear, $currentYear + 5);

// Funkcja obliczająca Wielkanoc (YYYY-MM-DD)
function easter_date_ymd($year) {
    if (function_exists('easter_date')) {
        return date('Y-m-d', easter_date($year));
    }
    // fallback
    $a = $year % 19;
    $b = intdiv($year, 100);
    $c = $year % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv(($b + 8), 25);
    $g = intdiv(($b - $f + 1), 3);
    $h = (19*$a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2*$e + 2*$i - $h - $k) % 7;
    $m = intdiv($a + 11*$h + 22*$l, 451);
    $month = intdiv($h + $l - 7*$m + 114, 31);
    $day = (($h + $l - 7*$m + 114) % 31) + 1;
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

// Tablica wynikowa: [ '2025-05-01' => 'Święto Pracy (PL, DE)' ]
$allHolidays = [];

// --- GENEROWANIE DAT ---
foreach ($yearsRange as $year) {
    // Polska stałe
    foreach ($fixedHolidaysPL as $mmdd => $name) {
        $date = sprintf('%04d-%s', $year, $mmdd);
        $allHolidays[$date][] = $name . ' (PL)';
    }

    // Niemcy stałe
    foreach ($fixedHolidaysDE as $mmdd => $name) {
        $date = sprintf('%04d-%s', $year, $mmdd);
        $allHolidays[$date][] = $name . ' (DE)';
    }

    // Niemcy regionalne
    foreach ($regionalHolidaysDE as $mmdd => $name) {
        $date = sprintf('%04d-%s', $year, $mmdd);
        $allHolidays[$date][] = $name . ' (DE)';
    }

    // Ruchome
    $easter = easter_date_ymd($year);

    $allHolidays[$easter][] = "Wielkanoc (PL, DE)";

    $dt = new DateTime($easter);
    $dt->modify('+1 day');
    $allHolidays[$dt->format('Y-m-d')][] = "Poniedziałek Wielkanocny (PL, DE)";

    $dt = new DateTime($easter);
    $dt->modify('+49 days');
    $allHolidays[$dt->format('Y-m-d')][] = "Zielone Świątki (DE)";

    $dt = new DateTime($easter);
    $dt->modify('+60 days');
    $allHolidays[$dt->format('Y-m-d')][] = "Boże Ciało (PL)";
}

// Format do JSON: tablica obiektów {date: "...", name: "..."}
$output = [];
foreach ($allHolidays as $date => $names) {
    $output[] = [
        'date' => $date,
        'name' => implode(', ', $names),
    ];
}

// Sortowanie po dacie
usort($output, fn($a, $b) => strcmp($a['date'], $b['date']));

echo json_encode($output);
