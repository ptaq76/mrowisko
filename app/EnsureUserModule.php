<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserModule
{
    /**
     * Sprawdza czy zalogowany użytkownik ma dostęp do danego modułu.
     * Admin ma dostęp do wszystkich modułów.
     *
     * Użycie w trasach: middleware('module:biuro')
     */
    public function handle(Request $request, Closure $next, string ...$modules): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin ma dostęp do wszystkiego
        if ($user->module === 'admin') {
            return $next($request);
        }

        // Sprawdź czy moduł użytkownika jest na liście dozwolonych
        if (in_array($user->module, $modules)) {
            return $next($request);
        }

        // Brak dostępu – przekieruj na własny dashboard
        return redirect()
            ->to('/'.$user->module.'/dashboard')
            ->with('error', 'Brak dostępu do tego modułu.');
    }
}
