<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('module')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $modules = User::MODULES;

        return view('admin.users.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:64', 'unique:users,login', 'regex:/^\S+$/'],
            'password' => ['required', 'string', 'min:6'],
            'module' => ['required', Rule::in(array_keys(User::MODULES))],
        ], [
            'name.required' => 'Podaj imię i nazwisko.',
            'login.required' => 'Podaj login.',
            'login.unique' => 'Ten login jest już zajęty.',
            'login.regex' => 'Login nie może zawierać spacji.',
            'password.required' => 'Podaj hasło.',
            'password.min' => 'Hasło musi mieć co najmniej 6 znaków.',
            'module.required' => 'Wybierz moduł.',
            'module.in' => 'Nieprawidłowy moduł.',
        ]);

        User::create([
            'name' => $request->name,
            'login' => $request->login,
            'password' => $request->password,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Użytkownik został utworzony.');
    }

    public function edit(User $user)
    {
        $modules = User::MODULES;

        return view('admin.users.edit', compact('user', 'modules'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:64', 'regex:/^\S+$/', Rule::unique('users', 'login')->ignore($user->id)],
            'module' => ['required', Rule::in(array_keys(User::MODULES))],
        ], [
            'name.required' => 'Podaj imię i nazwisko.',
            'login.required' => 'Podaj login.',
            'login.unique' => 'Ten login jest już zajęty.',
            'login.regex' => 'Login nie może zawierać spacji.',
            'module.required' => 'Wybierz moduł.',
        ]);

        // Nie można zmienić modułu jedynemu adminowi
        if ($user->module === 'admin' && $request->module !== 'admin') {
            return back()->withErrors(['module' => 'Nie można zmienić modułu jedynemu administratorowi.']);
        }

        $user->update([
            'name' => $request->name,
            'login' => $request->login,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Użytkownik został zaktualizowany.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [
            'password.required' => 'Podaj nowe hasło.',
            'password.min' => 'Hasło musi mieć co najmniej 6 znaków.',
        ]);

        $user->update(['password' => $request->password]);

        return redirect()->route('admin.users.index')
            ->with('success', "Hasło użytkownika {$user->name} zostało zmienione.");
    }

    public function destroy(User $user)
    {
        // Nie można usunąć samego siebie
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz usunąć własnego konta.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Użytkownik {$name} został usunięty.");
    }
}
