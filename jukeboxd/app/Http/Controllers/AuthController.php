<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // register
    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $credentials = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create($credentials);
        Auth::login($user); 

        return redirect()->intended('/')->with('success', 'Compte créé, bienvenue');
    }

    // Login
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Verify credentials
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // session fixation security
            return redirect()->intended('/')->with('success', 'Connexion résussie');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.',])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Déconnexion');
    }
}
