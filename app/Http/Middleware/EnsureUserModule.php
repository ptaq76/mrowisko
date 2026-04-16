<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserModule
{
    public function handle(Request $request, Closure $next, string $module): mixed
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin ma dostęp do wszystkiego
        if ($user->module === 'admin') {
            return $next($request);
        }

        if ($user->module !== $module) {
            abort(403, 'Brak dostępu do tego modułu.');
        }

        return $next($request);
    }
}
