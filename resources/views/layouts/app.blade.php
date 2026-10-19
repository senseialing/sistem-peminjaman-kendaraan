<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengelolaan Aset Kendaraan') — SPVMB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="icon" href="{{ asset('images/logo-spv.png') }}">
</head>
<body>
    @auth
    <header class="navbar">
        <div class="nav-container">
            <div class="nav-brand-section">
                <a href="{{ route('dashboard') }}" class="brand-logo" aria-label="SPVMB Dashboard">
                    <img src="{{ asset('images/logo-spv.png') }}" alt="SPV Logo" class="brand-logo-img">
                    <div class="brand-text-group">
                        <span class="brand-name">SPVMB</span>
                        <span class="brand-badge">Armada & Sirkulasi</span>
                    </div>
                </a>

                <button class="nav-mobile-toggle" id="mobileMenuToggle" type="button" aria-label="Menu Navigasi" onclick="this.closest('.navbar').classList.toggle('nav-open')">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <nav class="nav-menu" id="primaryNav">
                <ul class="nav-links">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="5" rx="1.5"></rect>
                                <rect x="14" y="12" width="7" height="9" rx="1.5"></rect>
                                <rect x="3" y="16" width="7" height="5" rx="1.5"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @if(Auth::user()->isKaryawan())
                    <li class="nav-item">
                        <a href="{{ route('peminjaman.create') }}" class="{{ request()->routeIs('peminjaman.create') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span>Ajukan Peminjaman</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.index*') && !request()->routeIs('peminjaman.create') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <span>Riwayat Saya</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isManajer())
                    <li class="nav-item">
                        <a href="{{ route('otorisasi.index') }}" class="{{ request()->routeIs('otorisasi.*') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            <span>Otorisasi Permohonan</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isStafAdmin())
                    <li class="nav-item">
                        <a href="{{ route('kendaraan.index') }}" class="{{ request()->routeIs('kendaraan.*') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="16" height="12" rx="2"></rect>
                                <polygon points="17 8 21 8 23 11 23 16 17 16 17 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                            <span>Data Armada</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sirkulasi.index') }}" class="{{ request()->routeIs('sirkulasi.index') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="8" cy="15" r="4"></circle>
                                <path d="m21 3-9.5 9.5"></path>
                                <path d="M15.5 7.5 18 10"></path>
                                <path d="m18 5 2.5 2.5"></path>
                            </svg>
                            <span>Sirkulasi Kunci</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sirkulasi.scan') }}" class="{{ request()->routeIs('sirkulasi.scan') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                <line x1="7" y1="12" x2="17" y2="12"></line>
                            </svg>
                            <span>Verifikasi QR</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Laporan & Audit</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="nav-right">
                    <div class="user-info-box">
                        <div class="user-details-text">
                            <span class="user-display-name">{{ Auth::user()->nama }}</span>
                            <span class="role-badge role-{{ strtolower(str_replace(' ', '', Auth::user()->hak_akses)) }}">
                                {{ Auth::user()->hak_akses }}
                            </span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Keluar dari sistem">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Logout</span>
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
            <div class="alert alert-success" role="alert">
                <div class="alert-content">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="alert-close" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" role="alert">
                <div class="alert-content">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="alert-close" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info" role="alert">
                <div class="alert-content">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" class="alert-close" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="footer-container">
            <div class="footer-brand-col">
                <span class="footer-brand-title">PT. Stainless Steel Primavalve Majubersama</span>
                <span class="footer-brand-desc">Sistem Pengelolaan & Sirkulasi Aset Kendaraan Dinas Operasional</span>
            </div>
            <div class="footer-tagline-col">
                <span class="footer-tagline">Think Flow, Think SPV</span>
                <span class="footer-copy">&copy; {{ date('Y') }} Hak Cipta Dilindungi</span>
            </div>
            <div class="footer-links-col">
                <span class="footer-link-item">Internal Operation</span>
                <span class="footer-sep">&middot;</span>
                <span class="footer-link-item">Protokol Keamanan Aset</span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
