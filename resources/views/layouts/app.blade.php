<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengelolaan Aset Kendaraan') - SPVMB</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="icon" href="{{ asset('images/logo-spv.png') }}">
</head>
<body>
    @auth
    <header class="navbar">
        <div class="nav-container">
            <div class="nav-brand-section">
                <a href="{{ route('dashboard') }}" class="brand-logo">
                    <img src="{{ asset('images/logo-spv.png') }}" alt="SPV Logo">
                    <div>
                        <span>SPVMB</span>
                        <div class="brand-badge">Asset Management</div>
                    </div>
                </a>

                <button class="nav-mobile-toggle" id="mobileMenuToggle" type="button" aria-label="Toggle navigation" onclick="this.closest('.navbar').classList.toggle('nav-open')">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <nav class="nav-menu">
                <ul class="nav-links">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            📊 Dashboard
                        </a>
                    </li>

                    @if(Auth::user()->isKaryawan())
                    <li class="nav-item">
                        <a href="{{ route('peminjaman.create') }}" class="{{ request()->routeIs('peminjaman.create') ? 'active' : '' }}">
                            ✍️ Ajukan Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.index*') && !request()->routeIs('peminjaman.create') ? 'active' : '' }}">
                            📑 Riwayat Saya
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isManajer())
                    <li class="nav-item">
                        <a href="{{ route('otorisasi.index') }}" class="{{ request()->routeIs('otorisasi.*') ? 'active' : '' }}">
                            🛡️ Otorisasi Permohonan
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isStafAdmin())
                    <li class="nav-item">
                        <a href="{{ route('kendaraan.index') }}" class="{{ request()->routeIs('kendaraan.*') ? 'active' : '' }}">
                            🚙 Data Armada & QR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sirkulasi.index') }}" class="{{ request()->routeIs('sirkulasi.index') ? 'active' : '' }}">
                            🔄 Sirkulasi Kunci
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sirkulasi.scan') }}" class="{{ request()->routeIs('sirkulasi.scan') ? 'active' : '' }}">
                            🔍 Verifikasi / Scan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                            📈 Laporan & Audit
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="nav-right">
                    <div class="user-info-box">
                        <div class="user-details-text">
                            <div style="font-weight: 700; font-size: 0.88rem;">{{ Auth::user()->nama }}</div>
                            <span class="role-badge {{ strtolower(str_replace(' ', '', Auth::user()->hak_akses)) }}">
                                {{ Auth::user()->hak_akses }}
                            </span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Keluar">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    @endauth

    <main class="main-wrapper">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: inherit;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>⚠️ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: inherit;">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <span>ℹ️ {{ session('info') }}</span>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: inherit;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div style="max-width: 1300px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <strong>PT. SPV</strong>
            </div>
            <div>
                &copy; {{ date('Y') }} PT. Stainless Steel Primavalve Majubersama.
                <em style="color: var(--primary);">Think Flow, Think SPV</em>
            </div>
            <div>
                Kebijakan Privasi &nbsp;&nbsp; Bantuan &nbsp;&nbsp; Kontak
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
