-- ============================================================
-- IMPORT: clients
-- Skąd:   mrowisko_stary.kontrahenci
-- Dokąd:  mrowisko_local.clients
-- Uwagi:  - Pomijamy kontrahentów tylko z transportujacy=1
--         - ID zachowane z oryginału
--         - Kraj domyślnie PL (DE do ręcznej korekty)
--         - bdo, email, phone, notes – brak w starej bazie
-- ============================================================

INSERT INTO mrowisko_local.clients 
    (id, name, short_name, nip, street, postal_code, city, country, type, salesman_id, is_active, created_at, updated_at)
SELECT
    id,
    nazwa,
    skrot,
    nip,
    adres,
    kod,
    miasto,
    'PL',
    CASE
        WHEN dostawca = 1 AND odbiorca = 1 THEN 'both'
        WHEN dostawca = 1 THEN 'pickup'
        WHEN odbiorca = 1 THEN 'sale'
        ELSE 'both'
    END,
    operator,
    1,
    created_at,
    updated_at
FROM mrowisko_stary.kontrahenci
WHERE NOT (transportujacy = 1 AND (dostawca = 0 OR dostawca IS NULL) AND (odbiorca = 0 OR odbiorca IS NULL));

-- Ustaw AUTO_INCREMENT na MAX(id) + 1
ALTER TABLE mrowisko_local.clients AUTO_INCREMENT = 1;

-- Weryfikacja
SELECT type, COUNT(*) as ile FROM mrowisko_local.clients GROUP BY type;
SELECT id, name, short_name, type, salesman_id FROM mrowisko_local.clients ORDER BY type LIMIT 20;
