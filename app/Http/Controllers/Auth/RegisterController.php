<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $this->ensureRegistrationOpen();

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->ensureRegistrationOpen();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // El registro público solo está disponible durante el "bootstrap" inicial,
        // cuando aún no existe ningún usuario. El primer usuario se convierte en Administrador.
        $defaultRole = Role::firstOrCreate(['nombre' => 'Administrador']);
        $user->roles()->attach($defaultRole->id);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registro exitoso.');
    }

    /**
     * El registro público solo se permite cuando todavía no existe ningún
     * usuario en el sistema (primera configuración / bootstrap). A partir
     * de ahí, los usuarios solo deben crearse desde "Administrar Usuarios".
     */
    private function ensureRegistrationOpen(): void
    {
        if (User::count() > 0) {
            abort(403, 'El registro público está deshabilitado.');
        }
    }
}
