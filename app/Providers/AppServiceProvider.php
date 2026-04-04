<?php

namespace App\Providers;

use App\Services\ImapGewichtsmeldungService;
use App\Services\ImapReklamacjeService;
use App\Services\PdfParserService;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
    }
}
