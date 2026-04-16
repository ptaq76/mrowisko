<?php

namespace App\Services;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;

class GusService
{
    public function getByNip(string $nip): array
    {
        $nip = preg_replace('/\D/', '', $nip);

        if (strlen($nip) !== 10) {
            return ['error' => 'Podaj prawidłowy NIP (10 cyfr)'];
        }

        if (! $this->validateNip($nip)) {
            return ['error' => 'Nieprawidłowy NIP — błędna cyfra kontrolna'];
        }

        try {
            $gus = new GusApi(config('services.gus.key'));
            $gus->login();

            $reports = $gus->getByNip($nip);

            if (empty($reports)) {
                return ['error' => 'Brak danych dla NIP: '.$nip];
            }

            $r = $reports[0];

            $adres = $r->getApartmentNumber() === ''
                ? $r->getStreet().' '.$r->getPropertyNumber()
                : $r->getStreet().' '.$r->getPropertyNumber().'/'.$r->getApartmentNumber();

            $kodRaw = preg_replace('/\D/', '', $r->getZipCode() ?? '');
            $kod = strlen($kodRaw) === 5
                ? substr($kodRaw, 0, 2).'-'.substr($kodRaw, 2)
                : ($r->getZipCode() ?? '');

            $nazwa = $r->getName();

            return [
                'nip' => $nip,
                'regon' => $r->getRegon() ?? '',
                'nazwa' => $nazwa,
                'skrot' => mb_strlen($nazwa) > 60 ? mb_substr($nazwa, 0, 57).'…' : $nazwa,
                'adres' => trim($adres),
                'miasto' => $r->getCity() ?? '',
                'kod' => $kod,
            ];

        } catch (InvalidUserKeyException) {
            return ['error' => 'Niepoprawny klucz GUS API'];
        } catch (NotFoundException) {
            return ['error' => 'Brak danych dla NIP: '.$nip];
        } catch (\Exception $e) {
            return ['error' => 'Błąd: '.$e->getMessage()];
        }
    }

    private function validateNip(string $nip): bool
    {
        $wagi = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $suma = 0;
        for ($i = 0; $i < 9; $i++) {
            $suma += (int) $nip[$i] * $wagi[$i];
        }

        return ($suma % 11) === (int) $nip[9];
    }
}
