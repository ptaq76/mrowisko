-- ============================================================
-- IMPORT: users
-- Skąd:   mrowisko_stary.users
-- Dokąd:  mrowisko_local.users
-- Data:   2024-01-01
-- Uwagi:  - Tomek (id=14) ma błędny moduł kierowca zamiast hakowiec
--         - Czarny Pan (id=18) ma błędny moduł plac zamiast czarnypan
--         - Do poprawienia przy finalnym imporcie z serwera
-- ============================================================

-- Przy ponownym imporcie: usuń obecnych użytkowników (oprócz seederowego admina)
-- i uruchom ponownie

DELETE FROM mrowisko_local.users WHERE login != 'admin';

INSERT INTO mrowisko_local.users (name, login, password, module, remember_token, created_at, updated_at)
SELECT
    name,
    username,
    password,
    CASE role_id
        WHEN 1 THEN 'admin'
        WHEN 2 THEN 'biuro'
        WHEN 3 THEN 'plac'
        WHEN 4 THEN 'kierowca'
        WHEN 5 THEN 'hakowiec'
        WHEN 6 THEN 'handlowiec'
        WHEN 7 THEN 'czarnypan'
        WHEN 8 THEN 'karchem'
        ELSE 'biuro'
    END,
    remember_token,
    created_at,
    updated_at
FROM mrowisko_stary.users;

-- Weryfikacja
SELECT id, name, login, module FROM mrowisko_local.users ORDER BY module;
