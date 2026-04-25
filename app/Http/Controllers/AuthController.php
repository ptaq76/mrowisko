<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    const MODULE_REDIRECTS = [
        'admin' => '/admin/dashboard',
        'biuro' => '/biuro/dashboard',
        'kierowca' => '/kierowca/dashboard',
        'hakowiec' => '/hakowiec/dashboard',
        'plac' => '/plac/dashboard',
        'handlowiec' => '/handlowiec/dashboard',
        'karchem' => '/karchem',
    ];

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToModule(Auth::user()->module);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login.required' => 'Podaj login.',
            'password.required' => 'Podaj hasło.',
        ]);

        $credentials = [
            'login' => $request->login,
            'password' => $request->password,
        ];

        // Remember me zawsze włączone
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            return $this->redirectToModule(Auth::user()->module);
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Nieprawidłowy login lub hasło.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectToModule(string $module)
    {
        $url = self::MODULE_REDIRECTS[$module] ?? '/';

        return redirect($url);
    }
}
