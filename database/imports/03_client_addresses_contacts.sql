-- ============================================================
-- IMPORT: client_addresses
-- Skąd:   mrowisko_stary.kontrahenci_adresy
-- Dokąd:  mrowisko_local.client_addresses
-- ============================================================

INSERT INTO mrowisko_local.client_addresses
    (id, client_id, city, postal_code, street, hours, notes, distance_km, latitude, longitude, created_at, updated_at)
SELECT
    id,
    id_kontrahenta,
    miasto,
    kod,
    adres,
    godziny,
    uwagi,
    dystans,
    latitude,
    longitude,
    created_at,
    updated_at
FROM mrowisko_stary.kontrahenci_adresy;

-- Ustaw AUTO_INCREMENT
ALTER TABLE mrowisko_local.client_addresses AUTO_INCREMENT = 1;

-- Weryfikacja
SELECT COUNT(*) as ile FROM mrowisko_local.client_addresses;
SELECT ca.id, c.short_name, ca.city, ca.street
FROM mrowisko_local.client_addresses ca
JOIN mrowisko_local.clients c ON c.id = ca.client_id
LIMIT 10;

-- ============================================================
-- IMPORT: client_contacts
-- Skąd:   mrowisko_stary.kontrahenci_kontakty
-- Dokąd:  mrowisko_local.client_contacts
-- Pomijamy: dzial = 'transport'
-- Mapowanie dzial: handlowy→handlowe, faktury→faktury, awizacje→awizacje
-- ============================================================

INSERT INTO mrowisko_local.client_contacts
    (id, client_id, category, name, email, phone, created_at, updated_at)
SELECT
    id,
    id_kontrahenta,
    CASE dzial
        WHEN 'handlowy'  THEN 'handlowe'
        WHEN 'faktury'   THEN 'faktury'
        WHEN 'awizacje'  THEN 'awizacje'
        ELSE 'handlowe'
    END,
    osoba,
    mail,
    telefon,
    `timestamp`,
    `timestamp`
FROM mrowisko_stary.kontrahenci_kontakty
WHERE dzial != 'transport';

-- Ustaw AUTO_INCREMENT
ALTER TABLE mrowisko_local.client_contacts AUTO_INCREMENT = 1;

-- Weryfikacja
SELECT category, COUNT(*) as ile FROM mrowisko_local.client_contacts GROUP BY category;
