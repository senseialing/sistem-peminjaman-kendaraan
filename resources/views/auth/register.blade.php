@extends('layouts.app')

@section('title', 'Daftar Karyawan Baru - SPVMB')

@section('content')
<div class="login-wrapper">
    <div class="login-card" style="max-width: 440px;">
        <img src="{{ asset('images/logo-spv.png') }}" alt="Logo SPV" class="login-logo" style="width: 80px;">
        <h1 class="login-title" style="font-size: 1.5rem;">Daftar Akun Karyawan</h1>
        <p class="login-subtitle">
            Registrasi mandiri staf untuk pengajuan peminjaman kendaraan dinas
        </p>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="login-input-group">
                <label for="nama">Nama Lengkap Pegawai</label>
                <div class="login-input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Muhammad Farhan" required autofocus>
                </div>
                @error('nama')
                    <div style="color: var(--primary); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-input-group">
                <label for="username">NIK / Username</label>
                <div class="login-input-wrapper">
                    <span class="input-icon">🔑</span>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: farhan" required>
                </div>
                <span class="form-hint">Digunakan untuk login ke sistem</span>
                @error('username')
                    <div style="color: var(--primary); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-input-group">
                <label for="password">Password</label>
                <div class="login-input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                @error('password')
                    <div style="color: var(--primary); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-input-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="login-input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="login-btn" style="margin-top: 0.5rem;">
                🚀 Buat Akun & Masuk
            </button>
        </form>

        <div class="login-footer">
            Sudah memiliki akun?
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>

        <div class="login-footer-brand">
            <div class="tagline">Think Flow, Think SPVMB</div>
            <div class="company">&copy; {{ date('Y') }} PT. STAINLESS STEEL PRIMAVALVE MAJUBERSAMA</div>
        </div>
    </div>
</div>
@endsection
