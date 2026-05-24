<?php

namespace App\Modules\Auth\Controllers;

use App\Kernel\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginWebController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->redirectTo());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo());
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function redirectTo(): string
    {
        $user = Auth::user();
        if (!$user) return '/login';

        return match (true) {
            $user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('tata-usaha') => '/dashboard',
            $user->hasRole('guru') || $user->hasRole('wali-kelas') => '/dashboard',
            $user->hasRole('siswa') => '/siswa/dashboard',
            $user->hasRole('orang-tua') => '/orang-tua/dashboard',
            default => '/dashboard',
        };
    }
}
