<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->nama} ({$user->hak_akses})!");
        }

        return back()->withErrors([
            'username' => 'Username atau password yang dimasukkan salah.',
        ])->onlyInput('username');
    }



    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Auto-generate employee code
        $count = User::where('hak_akses', 'Karyawan')->count() + 1;
        $idPengguna = 'EMP-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        while (User::find($idPengguna)) {
            $count++;
            $idPengguna = 'EMP-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $user = User::create([
            'id_pengguna' => $idPengguna,
            'nama'        => $request->nama,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'hak_akses'   => 'Karyawan',
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')
            ->with('success', "Akun Karyawan berhasil dibuat. ID Pegawai Anda: {$idPengguna}");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout.');
    }
}
