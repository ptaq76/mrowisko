<?php

namespace App\Providers;

use App\Services\ImapGewichtsmeldungService;
use App\Services\ImapReklamacjeService;
use App\Services\PdfParserService;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
// 1. DODAJEMY TEN IMPORT:
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Rejestracja serwisów reklamacji jako singletony
        $this->app->singleton(ImapReklamacjeService::class);
        $this->app->singleton(ImapGewichtsmeldungService::class);
        $this->app->singleton(PdfParserService::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('pl');

        // 2. DODAJEMY TĘ LINIĘ (ustawia limit znaków dla kluczy indeksów):
        Schema::defaultStringLength(191);
    }
}