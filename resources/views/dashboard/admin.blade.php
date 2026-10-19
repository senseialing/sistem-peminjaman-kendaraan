@extends('layouts.app')

@section('title', 'Dashboard Staf Admin — SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Operasional Armada</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Kelola inventaris armada dinas, sirkulasi kunci fisik, dan audit pelaporan.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('kendaraan.create') }}" class="btn btn-secondary btn-sm">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Tambah Armada</span>
        </a>
        <a href="{{ route('sirkulasi.scan') }}" class="btn btn-primary btn-sm">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                <line x1="7" y1="12" x2="17" y2="12"></line>
            </svg>
            <span>Scan Verifikasi QR</span>
        </a>
    </div>
</div>

<!-- Editorial KPIs -->
<div class="grid grid-cols-4" style="margin-bottom: 2.25rem;">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Total Unit Armada</span>
            <div class="stat-value">{{ $totalKendaraan }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Unit aktif terdaftar</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Armada Tersedia</span>
            <div class="stat-value" style="color: var(--success);">{{ $kendaraanTersedia }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Siap dinas operasional</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Sedang Dipinjam</span>
            <div class="stat-value" style="color: var(--info);">{{ $kendaraanDipinjam }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Dalam perjalanan dinas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Siap Serah Terima</span>
            <div class="stat-value" style="color: var(--warning);">{{ $siapSerahTerima->count() }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Menunggu kunci di admin</span>
        </div>
    </div>
</div>

<!-- Antrean Serah Terima Kunci -->
<div class="card" style="margin-bottom: 2.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Antrean Serah Terima Kunci (Check-Out)</h2>
            <p style="color: var(--text-muted); font-size: 0.86rem; margin-top: 0.2rem;">Permohonan disetujui Manajer. Serahkan kunci fisik kepada peminjam dan konfirmasi di bawah:</p>
        </div>
        <span class="badge badge-dipinjam"><span class="badge-dot"></span> {{ $siapSerahTerima->count() }} Menunggu Kunci</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Peminjam</th>
                    <th>Kendaraan</th>
                    <th>Tanggal Dinas</th>
                    <th>Tujuan Operasional</th>
                    <th style="text-align: right;">Aksi Serah Terima</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siapSerahTerima as $trx)
                <tr>
                    <td><code>{{ $trx->id_transaksi }}</code></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.76rem; color: var(--text-subtle);">NIK: {{ $trx->pengguna->username }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.76rem; color: var(--primary);"><code>{{ $trx->kendaraan->plat_nomor }}</code></div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                        @if($trx->tanggal_selesai && $trx->tanggal_selesai->ne($trx->tanggal_pengajuan))
                            <div style="font-size: 0.75rem; color: var(--text-muted);">s/d {{ $trx->tanggal_selesai->translatedFormat('d M Y') }} ({{ $trx->tanggal_pengajuan->diffInDays($trx->tanggal_selesai) + 1 }} hari)</div>
                        @endif
                    </td>
                    <td style="max-width: 250px; font-size: 0.85rem; color: var(--text-muted);">
                        {{ $trx->tujuan_operasional }}
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.serah-terima', $trx->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi serah terima kunci fisik kendaraan kepada peminjam?')">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <path d="m21 3-9.5 9.5"></path>
                                    <path d="M15.5 7.5 18 10"></path>
                                </svg>
                                <span>Serahkan Kunci</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.25rem 1rem; color: var(--text-subtle);">
                        Tidak ada antrean serah terima kunci saat ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Kendaraan Sedang Digunakan -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Kendaraan Sedang Dalam Penggunaan (Check-In Pengembalian)</h2>
            <p style="color: var(--text-muted); font-size: 0.86rem; margin-top: 0.2rem;">Armada yang saat ini berada di luar kantor. Konfirmasi setelah kunci dan unit dikembalikan.</p>
        </div>
        <span class="badge badge-tersedia"><span class="badge-dot"></span> {{ $sedangDipinjam->count() }} Sedang Dinas</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket & Waktu Serah Terima</th>
                    <th>Peminjam</th>
                    <th>Kendaraan</th>
                    <th>Status Sirkulasi</th>
                    <th style="text-align: right;">Aksi Pengembalian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sedangDipinjam as $trx)
                <tr>
                    <td>
                        <code>{{ $trx->id_transaksi }}</code>
                        <div style="font-size: 0.76rem; color: var(--text-subtle); margin-top: 0.2rem;">
                            Keluar: {{ $trx->timestamp_serah_terima ? $trx->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }} WIB
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-subtle);">{{ $trx->tujuan_operasional }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.76rem; color: var(--primary);"><code>{{ $trx->kendaraan->plat_nomor }}</code></div>
                    </td>
                    <td>
                        @if($trx->status_sirkulasi === 'Menunggu ACC Pengembalian')
                            <span class="badge badge-menunggu-acc"><span class="badge-dot"></span> Pengembalian Diajukan</span>
                        @else
                            <span class="badge badge-dipinjam"><span class="badge-dot"></span> Sedang Digunakan</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.pengembalian', $trx->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi bahwa kendaraan telah diterima kembali dalam kondisi baik?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Terima Kembali</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2.25rem 1rem; color: var(--text-subtle);">
                        Tidak ada kendaraan yang sedang dipinjam saat ini. Seluruh armada berada di pool.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
