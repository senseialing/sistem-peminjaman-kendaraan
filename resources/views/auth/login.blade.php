@extends('layouts.app')

@section('title', 'Login Operasional — SPVMB')

@section('content')
    <div class="login-wrapper">
        <div class="login-card">
            <img src="{{ asset('images/logo-spv.png') }}" alt="Logo SPV" class="login-logo">
            <h1 class="login-title">LOGIN Pegawai</h1>
            <p class="login-subtitle">Sistem Pengelolaan & Sirkulasi Aset Kendaraan Dinas Operasional</p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="login-input-group">
                    <label for="username">Nama Pengguna / NIK</label>
                    <div class="login-input-wrapper">
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan NIK atau username" required autofocus>
                    </div>
                    @error('username')
                        <div style="color: var(--primary); font-size: 0.78rem; margin-top: 0.35rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-input-group">
                    <label for="password">Kata Sandi</label>
                    <div class="login-input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                    </div>
                    @error('password')
                        <div style="color: var(--primary); font-size: 0.78rem; margin-top: 0.35rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div
                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; margin-top: 0.5rem;">
                    <label
                        style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: var(--text-muted); cursor: pointer;">
                        <input type="checkbox" name="remember">
                        Ingat Sesi Ini
                    </label>
                    <span style="font-size: 0.82rem; color: var(--text-subtle);">Akses Khusus Internal</span>
                </div>

                <button type="submit" class="login-btn">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="login-footer">
                Pegawai baru belum memiliki akun?
                <a href="{{ route('register') }}">Registrasi Akun Karyawan</a>
            </div>

            <div class="login-footer-brand">
                <div class="tagline">Think Flow, Think SPV</div>
                <div class="company">&copy; {{ date('Y') }} PT. Stainless Steel Primavalve Majubersama</div>
            </div>
        </div>
    </div>
@endsection