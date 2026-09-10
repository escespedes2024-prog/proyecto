<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', [
            'registrationOpen' => \App\Models\User::count() === 0,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // OWASP ASVS 2.4.3/2.4.4: si el coste del algoritmo (config/hashing.php)
            // aumenta en el futuro, la contraseña se re-hashea con el nuevo coste
            // en el siguiente inicio de sesión correcto (rehash bajo demanda).
            if (Hash::needsRehash(auth()->user()->password)) {
                auth()->user()->update(['password' => Hash::make($request->password)]);
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Sesión iniciada con éxito.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sesión cerrada con éxito.');
    }
}
