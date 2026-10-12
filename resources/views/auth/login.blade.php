@extends('layouts.app')

@section('title', 'Login - SPVMB Asset Management')

@section('content')
    <div class="login-wrapper">
        <div class="login-card">
            <img src="{{ asset('images/logo-spv.png') }}" alt="Logo SPV" class="login-logo">
            <h1 class="login-title">Login</h1>
            <p class="login-subtitle">Sistem Informasi Pengelolaan Aset Kendaraan Dinas</p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="login-input-group">
                    <label for="username">Nama Pengguna</label>
                    <div class="login-input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="" required autofocus>
                    </div>
                    @error('username')
                        <div style="color: var(--primary); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-input-group">
                    <label for="password">Kata Sandi</label>
                    <div class="login-input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password"
                            placeholder="" required>
                    </div>
                    @error('password')
                        <div style="color: var(--primary); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <label
                        style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                        <input type="checkbox" name="remember">
                        Ingat Saya
                    </label>
                    <span style="font-size: 0.82rem; color: var(--primary); font-weight: 500; cursor: pointer;">Lupa Kata Sandi?</span>
                </div>

                <button type="submit" class="login-btn">
                    Masuk ➜
                </button>
            </form>

            <div class="login-footer">
                Belum memiliki akun Karyawan?
                <a href="{{ route('register') }}">Daftar di sini</a>
            </div>

            <div class="login-footer-brand">
                <div class="tagline">Think Flow, Think SPVMB</div>
                <div class="company">&copy; {{ date('Y') }} PT. STAINLESS STEEL PRIMAVALVE MAJUBERSAMA</div>
            </div>
        </div>
    </div>
@endsection