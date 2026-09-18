<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    // Tampilkan Halaman Form signin
    public function showLoginForm()
    {
        // Jika user SUDAH login, baru arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route(
                Auth::user()->role === 'admin' ? 'barang-keluar.index' : 'dashboard'
            );
        }

        return view('pages.auth.signin');
    }

    public function signin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek email & password ke database
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $defaultRoute = Auth::user()->role === 'admin'
                ? route('barang-keluar.index')
                : route('dashboard');

            return redirect()->intended($defaultRoute)->with('success', 'Selamat datang kembali!');
        }

        // Jika gagal signin
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses Logout Total
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('success', 'Anda telah berhasil keluar.');
    }
}
