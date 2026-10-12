@extends('layouts.app')

@section('title', 'Dashboard Staf Admin - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Staf Admin Operasional</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Kelola inventaris armada dinas, sirkulasi kunci fisik, dan audit pelaporan.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('kendaraan.create') }}" class="btn btn-secondary btn-sm">
            ➕ Tambah Armada
        </a>
        <a href="{{ route('sirkulasi.scan') }}" class="btn btn-primary btn-sm">
            🔍 Scan / Verifikasi QR
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon purple">🚙</div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalKendaraan }}</div>
            <div class="stat-label">Total Unit Armada</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $kendaraanTersedia }}</div>
            <div class="stat-label">Armada Tersedia</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">🚗</div>
        <div class="stat-info">
            <div class="stat-value">{{ $kendaraanDipinjam }}</div>
            <div class="stat-label">Sedang Dipinjam</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">🔑</div>
        <div class="stat-info">
            <div class="stat-value">{{ $siapSerahTerima->count() }}</div>
            <div class="stat-label">Siap Serah Terima</div>
        </div>
    </div>
</div>

<!-- Antrean Serah Terima Kunci -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Antrean Serah Terima Kunci (Check-Out)</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Permohonan telah disetujui Manajer. Serahkan kunci fisik kepada peminjam dan konfirmasi di bawah:</p>
        </div>
        <span class="badge badge-dipinjam">{{ $siapSerahTerima->count() }} Menunggu Kunci</span>
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
                    <td><strong style="font-family: var(--font-mono);">{{ $trx->id_transaksi }}</strong></td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">NIK: {{ $trx->pengguna->username }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">{{ $trx->kendaraan->plat_nomor }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                        @if($trx->tanggal_selesai && $trx->tanggal_selesai->ne($trx->tanggal_pengajuan))
                            <div style="font-size: 0.75rem; color: var(--primary);">s/d {{ $trx->tanggal_selesai->translatedFormat('d M Y') }} ({{ $trx->tanggal_pengajuan->diffInDays($trx->tanggal_selesai) + 1 }} hr)</div>
                        @endif
                    </td>
                    <td style="max-width: 250px; font-size: 0.85rem; color: var(--text-muted);">
                        {{ $trx->tujuan_operasional }}
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.serah-terima', $trx->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi serah terima kunci fisik kendaraan kepada peminjam?')">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                🔑 Serahkan Kunci
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Tidak ada antrean serah terima kunci saat ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Kendaraan Sedang Digunakan / Menunggu Pengembalian -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid #10b981;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Kendaraan Sedang Dalam Penggunaan (Check-In Pengembalian)</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Armada yang saat ini berada di luar kantor. Klik "Terima Kembali" setelah kunci & kendaraan dikembalikan.</p>
        </div>
        <span class="badge badge-tersedia">{{ $sedangDipinjam->count() }} Sedang Jalan</span>
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
                        <strong style="font-family: var(--font-mono);">{{ $trx->id_transaksi }}</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                            Keluar: {{ $trx->timestamp_serah_terima ? $trx->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }} WIB
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $trx->tujuan_operasional }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">{{ $trx->kendaraan->plat_nomor }}</div>
                    </td>
                    <td>
                        @if($trx->status_sirkulasi === 'Menunggu ACC Pengembalian')
                            <span class="badge badge-menunggu-acc">🔔 Pengembalian Diajukan</span>
                        @else
                            <span class="badge badge-dipinjam">Sedang Digunakan</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.pengembalian', $trx->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi bahwa kendaraan telah diterima kembali dalam kondisi baik?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                🔄 Terima Kembali
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Tidak ada kendaraan yang sedang dipinjam saat ini. Semua armada berada di pool.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
