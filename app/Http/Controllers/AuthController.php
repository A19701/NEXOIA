<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.acceso');
    }

    public function showRegister()
    {
        return view('auth.acceso');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (strlen($value) > 72) {
                        $fail('La contraseña es demasiado larga.');
                    }
                }
            ]
        ]);

        $usuario = DB::transaction(function () use ($datos) {

            $usuario = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password'])
            ]);

            $usuario->perfil()->create([
                'memoria_activa' => false
            ]);

            return $usuario;
        });

        Auth::login($usuario);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($datos)) {

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Correo o contraseña incorrectos.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}