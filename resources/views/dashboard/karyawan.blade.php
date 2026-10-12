@extends('layouts.app')

@section('title', 'Dashboard Karyawan - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Karyawan</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong> (ID: {{ Auth::user()->id_pengguna }}). Kelola permohonan kendaraan dinas Anda di sini.</p>
    </div>
    <div>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            ➕ Ajukan Peminjaman Baru
        </a>
    </div>
</div>

@if($peminjamanAktif)
<div class="card" style="margin-bottom: 2rem; border-left: 5px solid var(--primary); background: linear-gradient(135deg, rgba(225, 29, 45, 0.08), var(--bg-card));">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui Manajer</span>
                @if($peminjamanAktif->timestamp_serah_terima)
                    <span class="badge badge-dipinjam"><span class="badge-dot"></span> Sedang Dalam Penggunaan</span>
                @else
                    <span class="badge badge-pending"><span class="badge-dot"></span> Menunggu Serah Terima Kunci di Admin</span>
                @endif
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">
                {{ $peminjamanAktif->kendaraan->nama_kendaraan }} — <code style="font-family: var(--font-mono); color: var(--primary);">{{ $peminjamanAktif->kendaraan->plat_nomor }}</code>
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                <strong>Tiket:</strong> {{ $peminjamanAktif->id_transaksi }} | 
                <strong>Periode Dinas:</strong> {{ $peminjamanAktif->tanggal_pengajuan->translatedFormat('d M Y') }}@if($peminjamanAktif->tanggal_selesai && $peminjamanAktif->tanggal_selesai->ne($peminjamanAktif->tanggal_pengajuan)) s/d {{ $peminjamanAktif->tanggal_selesai->translatedFormat('d M Y') }} ({{ $peminjamanAktif->tanggal_pengajuan->diffInDays($peminjamanAktif->tanggal_selesai) + 1 }} Hari)@endif |
                <strong>Tujuan:</strong> {{ $peminjamanAktif->tujuan_operasional }}
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('peminjaman.show', $peminjamanAktif->id_transaksi) }}" class="btn btn-secondary btn-sm">
                🎫 Lihat Tiket Digital & QR
            </a>

            @if($peminjamanAktif->timestamp_serah_terima && !$peminjamanAktif->timestamp_kembali)
                <form action="{{ route('peminjaman.kembalikan', $peminjamanAktif->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah selesai dinas dan ingin mengembalikan kendaraan?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        🔄 Ajukan Selesai / Pengembalian
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-3" style="margin-bottom: 2.5rem;">
    <div class="stat-card">
        <div class="stat-icon blue">🚗</div>
        <div class="stat-info">
            <div class="stat-value">{{ $peminjamanAktif ? 1 : 0 }}</div>
            <div class="stat-label">Peminjaman Aktif Saat Ini</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">⏳</div>
        <div class="stat-info">
            <div class="stat-value">{{ $countPending }}</div>
            <div class="stat-label">Permohonan Menunggu Otorisasi</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $countApproved }}</div>
            <div class="stat-label">Total Permohonan Disetujui</div>
        </div>
    </div>
</div>

<!-- Kendaraan Tersedia -->
<div style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 700;">Armada Siap Digunakan</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Pilih salah satu armada dinas yang saat ini tersedia untuk tugas luar kantor.</p>
        </div>
    </div>

    <div class="grid grid-cols-3">
        @forelse($kendaraanTersedia as $k)
        <div class="vehicle-card">
            <img src="{{ $k->foto_url }}" alt="{{ $k->nama_kendaraan }}" class="vehicle-img">
            <div class="vehicle-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.2rem;">{{ $k->nama_kendaraan }}</h3>
                        <span style="font-family: var(--font-mono); font-weight: 600; color: var(--primary); font-size: 0.85rem; background: #eff6ff; padding: 2px 6px; border-radius: 4px;">
                            {{ $k->plat_nomor }}
                        </span>
                    </div>
                    <span class="badge badge-tersedia">Tersedia</span>
                </div>

                <div style="margin: 0.75rem 0; font-size: 0.85rem; color: var(--text-muted); display: flex; gap: 1rem;">
                    <span>👥 Kapasitas: <strong>{{ $k->kapasitas }} Orang</strong></span>
                    <span>🏷️ Kode: <strong>{{ $k->id_kendaraan }}</strong></span>
                </div>

                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <a href="{{ route('peminjaman.create', ['id_kendaraan' => $k->id_kendaraan]) }}" class="btn btn-primary btn-sm" style="width: 100%;">
                        Pilih Unit Ini &rarr;
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <p style="color: var(--text-muted);">Saat ini seluruh armada dinas sedang dalam penggunaan atau perawatan.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Riwayat Pengajuan Karyawan -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700;">Riwayat Pengajuan Peminjaman Saya</h2>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary btn-sm">Lihat Semua &rarr;</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Kendaraan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tujuan Operasional</th>
                    <th>Otorisasi Manajer</th>
                    <th>Status Sirkulasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatTransaksi as $trx)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); font-size: 0.88rem; color: var(--text-main);">
                            {{ $trx->id_transaksi }}
                        </strong>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->kendaraan->nama_kendaraan ?? '-' }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); font-family: var(--font-mono);">{{ $trx->kendaraan->plat_nomor ?? '-' }}</div>
                    </td>
                    <td>{{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $trx->tujuan_operasional }}">
                        {{ $trx->tujuan_operasional }}
                    </td>
                    <td>
                        @if($trx->status_otorisasi === 'Approved')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui</span>
                        @elseif($trx->status_otorisasi === 'Rejected')
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Ditolak</span>
                        @else
                            <span class="badge badge-pending"><span class="badge-dot"></span> Menunggu</span>
                        @endif
                    </td>
                    <td>
                        @if($trx->status_sirkulasi === 'Menunggu ACC Pengembalian')
                            <span class="badge badge-menunggu-acc">ACC Pengembalian</span>
                        @else
                            <span class="badge badge-tersedia">{{ $trx->status_sirkulasi }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('peminjaman.show', $trx->id_transaksi) }}" class="btn btn-secondary btn-sm" title="Lihat Tiket QR">
                            🎫 Tiket
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Belum ada riwayat pengajuan peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
