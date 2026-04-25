<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

</head>
<body>
    <p class="right">Wydruk utworzono: <b>{{ now()->format('Y-m-d H:i') }}</b></p>
    
    <!-- Główna tabela nagłówkowa -->
    <table>
        <tr>
            <td class="bg bold" colspan="6" style="font-size: 9pt">KARTA PRZEKAZANIA ODPADÓW</td>
        </tr>
        <tr>
            <td class="bg" style="width: 17%;">Numer karty</td>
            <td style="width: 16%;">{{ $karta['numer_karty'] ?? '' }}</td>
            <td class="bg" style="width: 17%;">Status karty</td>
            <td style="width: 16%;">{{ $karta['status_karty'] ?? '' }}</td>
            <td class="bg" style="width: 17%;">Rok kalendarzowy</td>
            <td style="width: 17%;">{{ $karta['rok_kalendarzowy'] ?? '' }}</td>
        </tr>
        
        <!-- Sekcja z danymi podmiotów -->
        <tr>
            <td class="bg bold" colspan="2" style="width: 33%;font-size: 9pt">DANE PRZEKAZUJĄCEGO ODPADY</td>
            <td class="bg bold" colspan="2" style="width: 33%;font-size: 9pt">DANE TRANSPORTUJĄCEGO ODPADY</td>
            <td class="bg bold" colspan="2" style="width: 34%;font-size: 9pt">DANE PRZEJMUJĄCEGO ODPADY</td>
        </tr>
        
        <tr>
            <td class="bg" colspan="2">Nazwa lub Imię i Nazwisko</td>
            <td class="bg" colspan="2">Nazwa lub Imię i Nazwisko</td>
            <td class="bg" colspan="2">Nazwa lub Imię i Nazwisko</td>
        </tr>
        
        <tr>
            <td colspan="2">{{ $karta['nadawca_nazwa'] ?? ' ' }}</td>
            <td colspan="2" rowspan="8">{{ $karta['przewoznik_nazwa'] ?? ' ' }}</td>
            <td colspan="2">{{ $karta['odbiorca_nazwa'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg" colspan="2">Adres</td>
            <td class="bg" colspan="2">Adres</td>
        </tr>
        
        <tr>
            <td colspan="2">{{ $karta['nadawca_adres'] ?? ' ' }}</td>
            <td colspan="2">{{ $karta['odbiorca_adres'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td colspan="2">Wytwarzanie odpadów - w wyniku świadczenia usług (w rozumieniu art. 3 ust. 1 pkt 32 ustawy o odpadach) i/lub działalności w zakresie obiektów liniowych (w rozumieniu art. 3 pkt 3a ustawy - Prawo budowlane) - NIE</td>
            <td colspan="2" rowspan="5"></td>
        </tr>
        
        <tr>
            <td class="bg" colspan="2">Miejsce wytwarzania odpadów</td>
        </tr>
        
        <tr>
            @if(!empty($karta['miejsce_wytworzenia_wojewodztwo']) || !empty($karta['miejsce_wytworzenia_powiat']) || !empty($karta['miejsce_wytworzenia_gmina']))
    <td colspan="2">
        @if(!empty($karta['miejsce_wytworzenia_wojewodztwo']))
            <b>Województwo:</b> {{ $karta['miejsce_wytworzenia_wojewodztwo'] }}, 
        @endif
        @if(!empty($karta['miejsce_wytworzenia_powiat']))
            <b>Powiat:</b> {{ $karta['miejsce_wytworzenia_powiat'] }}, 
        @endif
        @if(!empty($karta['miejsce_wytworzenia_gmina']))
            <b>Gmina:</b> {{ $karta['miejsce_wytworzenia_gmina'] }}
        @endif
    </td>
@else
    <td colspan="2" style="height: 2px;padding:0;margin:0;"></td>
@endif
        </tr>
       
        <tr>
            <td class="bg" colspan="2">Dodatkowe informacje o miejscu wytwarzania odpadów</td>
        </tr>
        
        <tr>
            <td colspan="2" style="height: 2px;padding:0;margin:0;"></td>
        </tr>
        
        <!-- Miejsce prowadzenia działalności -->
        <tr>
            <td class="bg bold" colspan="2" style="width: 32%;font-size: 9pt">MIEJSCE PROWADZENIA DZIAŁALNOŚCI</td>
            <td class="bg bold" colspan="2" style="width: 33%;">Adres</td>
            <td class="bg bold" colspan="2" style="width: 34%;font-size: 9pt">MIEJSCE PROWADZENIA DZIAŁALNOŚCI</td>
        </tr>
        
        <tr>
            <td class="bg" style="width: 16.5%;">Numer miejsca prowadzenia działalności</td>
            <td style="width: 16.5%;">{{ $karta['nadawca_numer_identyfikacyjny'] ?? ' ' }}</td>
            <td colspan="2" rowspan="3">{{ $karta['przewoznik_adres'] ?? ' ' }}</td>
            <td class="bg" style="width: 16.5%;">Numer miejsca prowadzenia działalności</td>
            <td style="width: 16.5%;">{{ $karta['odbiorca_numer_identyfikacyjny'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">Nazwa miejsca prowadzenia działalności</td>
            <td>{{ $karta['nadawca_nazwa_eup'] ?? ' ' }}</td>
            <td class="bg">Nazwa miejsca prowadzenia działalności</td>
            <td>{{ $karta['odbiorca_nazwa_eup'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">Adres miejsca prowadzenia działalności</td>
            <td>{{ $karta['nadawca_adres_eup'] ?? ' ' }}</td>
            <td class="bg">Adres miejsca prowadzenia działalności</td>
            <td>{{ $karta['odbiorca_adres_eup'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">Numer rejestrowy</td>
            <td>{{ $karta['nadawca_numer_identyfikacyjny'] ?? ' ' }}</td>
            <td class="bg">Numer rejestrowy</td>
            <td>{{ $karta['przewoznik_numer_identyfikacyjny'] ?? ' ' }}</td>
            <td class="bg">Numer rejestrowy</td>
            <td>{{ $karta['odbiorca_numer_identyfikacyjny'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">NIP</td>
            <td>{{ $karta['nadawca_nip'] ?? ' ' }}</td>
            <td class="bg">NIP</td>
            <td>{{ $karta['przewoznik_nip'] ?? ' ' }}</td>
            <td class="bg">NIP</td>
            <td>{{ $karta['odbiorca_nip'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">NIP EUROPEJSKI</td>
            <td>{{ $karta['nadawca_nip_eu'] ?? ' ' }}</td>
            <td class="bg">NIP EUROPEJSKI</td>
            <td>{{ $karta['przewoznik_nip_eu'] ?? ' ' }}</td>
            <td class="bg">NIP EUROPEJSKI</td>
            <td>{{ $karta['odbiorca_nip_eu'] ?? ' ' }}</td>
        </tr>
        
        <!-- Informacje o odpadach -->
        <tr>
            <td class="bg bold" colspan="6" style="font-size: 9pt">INFORMACJE DOTYCZĄCE PRZEKAZYWANYCH ODPADÓW</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" style="width: 23%;border-top: none">Rodzaj procesu przetwarzania, któremu powinny zostać poddane odpady</td>
            <td style="border-top: none"></td>
        </tr>
        
        <tr>
            <td class="bg">Numer certyfikatu oraz numery pojemników</td>
            <td></td>
        </tr>
        
        <tr>
            <td class="bg" style="border-bottom: none">Kod i rodzaj odpadów</td>
            <td style="border-bottom: none">{{ $karta['kod_i_opis_odpadu'] ?? ' ' }}</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td style="width: 40%;">Kod ex - <b>NIE</b></td>
            <td class="bg" style="width: 18%;">Rodzaj odpadu ex</td>
            <td></td>
        </tr>
        
        <tr>
            <td>Zmiana statusu odpadów niebezpiecznych na odpady inne niż niebezpieczne - <b>NIE</b></td>
            <td class="bg">Rodzaj odpadu</td>
            <td></td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" style="width: 23%;border-bottom: none;border-top: none;">Masa odpadów [Mg]</td>
            <td style="border-bottom: none;border-top: none;">{{ $karta['masa_odpadu'] ?? ' ' }}</td>
        </tr>
        
        <!-- Transport -->
        <tr>
            <td class="bg bold" colspan="2" style="font-size: 9pt;border-bottom: none;">INFORMACJE DOTYCZĄCE TRANSPORTU</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" style="width: 23%;">Numer rejestracyjny środka transportu/Rodzaj<br> środka transportu</td>
            <td colspan="3">{{ $karta['numer_rejestracyjny_pojazdu'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg">Data rozpoczęcia transportu</td>
            <td class="bg">Godzina rozpoczęcia transportu</td>
            <td class="bg">Faktyczna data rozpoczęcia transportu</td>
            <td class="bg">Faktyczna godzina rozpoczęcia transportu</td>
        </tr>
        
        <tr>
            <td>{{ $karta['planowana_data_transportu'] ?? ' ' }}</td>
            <td>{{ $karta['planowana_godzina_transportu'] ?? ' ' }}</td>
            <td>{{ $karta['rzeczywista_data_transportu'] ?? ' ' }}</td>
            <td>{{ $karta['rzeczywista_godzina_transportu'] ?? ' ' }}</td>
        </tr>
        
        <!-- Przejęcie odpadów -->
        <tr>
            <td class="bg bold" colspan="4" style="font-size: 9pt">INFORMACJE O PRZEJĘCIU ODPADÓW</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" style="width: 33%;border-top: none">Masa przejętych odpadów [Mg]</td>
            <td class="bg" style="width: 33%;border-top: none">Data potwierdzenia przejęcia odpadów</td>
            <td class="bg" style="border-top: none">Godzina potwierdzenia przejęcia odpadów</td>
        </tr>
        
        <tr>
            <td style="border-bottom: none;">{{ $karta['skorygowana_masa_odpadu'] ?? ' ' }}</td>
            <td style="border-bottom: none;">{{ $karta['data_potwierdzenia_odbioru'] ?? ' ' }}</td>
            <td style="border-bottom: none;">{{ $karta['godzina_potwierdzenia_odbioru'] ?? ' ' }}</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" style="width: 25%;">Uwagi</td>
            <td>{{ $karta['uwagi'] ?? ' ' }}</td>
        </tr>
        
        <!-- Informacje o karcie -->
        <tr>
            <td class="bg bold" colspan="2" style="font-size: 9pt">INFORMACJE O KARCIE PRZEKAZANIA ODPADÓW</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td class="bg" colspan="2" style="width: 32%;border-top: none"><b>Zatwierdzenie karty przekazania odpadów</b></td>
            <td class="bg" colspan="2" style="width: 33%;border-top: none"><b>Potwierdzenie transportu odpadów</b></td>
            <td class="bg" colspan="2" style="border-top: none"><b>Potwierdzenie przejęcia odpadów</b></td>
        </tr>
        
        <tr>
            <td class="bg" colspan="2">Imię i nazwisko osoby zatwierdzającej kartę</td>
            <td class="bg" colspan="2">Imię i nazwisko osoby potwierdzającej transport</td>
            <td class="bg" colspan="2">Imię i nazwisko osoby potwierdzającej przejęcie</td>
        </tr>
        
        <tr>
            <td colspan="2">{{ $karta['zatwierdzone_przez'] ?? ' ' }}</td>
            <td colspan="2">{{ $karta['transport_potwierdzony_przez'] ?? ' ' }}</td>
            <td colspan="2">{{ $karta['odbiór_potwierdzony_przez'] ?? ' ' }}</td>
        </tr>
        
        <tr>
            <td class="bg" style="width: 16%;">Data</td>
            <td class="bg" style="width: 17%;">Godzina</td>
            <td class="bg" style="width: 16%;">Data</td>
            <td class="bg" style="width: 17%;">Godzina</td>
            <td class="bg" style="width: 16%;">Data</td>
            <td class="bg" style="width: 18%;">Godzina</td>
        </tr>
        
        <tr>
            <td>{{ $karta['data_zatwierdzenia'] ?? ' ' }}</td>
            <td>{{ $karta['godzina_zatwierdzenia'] ?? ' ' }}</td>
            <td>{{ $karta['data_potwierdzenia_transportu'] ?? ' ' }}</td>
            <td>{{ $karta['godzina_potwierdzenia_transportu'] ?? ' ' }}</td>
            <td>{{ $karta['data_potwierdzenia_odbioru'] ?? ' ' }}</td>
            <td>{{ $karta['godzina_potwierdzenia_odbioru'] ?? ' ' }}</td>
        </tr>
    </table>
</body>
</html>