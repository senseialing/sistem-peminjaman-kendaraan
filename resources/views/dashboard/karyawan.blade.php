@extends('layouts.app')

@section('title', 'Dashboard Karyawan — SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Permohonan Armada</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Kelola permohonan kendaraan dinas operasional Anda di sini.</p>
    </div>
    <div>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Ajukan Peminjaman Baru</span>
        </a>
    </div>
</div>

@if($peminjamanAktif)
<div class="card" style="margin-bottom: 2.25rem; border-left: 3px solid var(--primary);">
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
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">
                {{ $peminjamanAktif->kendaraan->nama_kendaraan }} — <code style="color: var(--primary);">{{ $peminjamanAktif->kendaraan->plat_nomor }}</code>
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.35rem; line-height: 1.5;">
                <strong>Tiket:</strong> <code>{{ $peminjamanAktif->id_transaksi }}</code> &nbsp;&middot;&nbsp; 
                <strong>Periode:</strong> {{ $peminjamanAktif->tanggal_pengajuan->translatedFormat('d M Y') }}@if($peminjamanAktif->tanggal_selesai && $peminjamanAktif->tanggal_selesai->ne($peminjamanAktif->tanggal_pengajuan)) s/d {{ $peminjamanAktif->tanggal_selesai->translatedFormat('d M Y') }} ({{ $peminjamanAktif->tanggal_pengajuan->diffInDays($peminjamanAktif->tanggal_selesai) + 1 }} hari)@endif &nbsp;&middot;&nbsp; 
                <strong>Tujuan:</strong> {{ $peminjamanAktif->tujuan_operasional }}
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('peminjaman.show', $peminjamanAktif->id_transaksi) }}" class="btn btn-secondary btn-sm">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <line x1="7" y1="8" x2="17" y2="8"></line>
                    <line x1="7" y1="12" x2="17" y2="12"></line>
                </svg>
                <span>Tiket Digital & QR</span>
            </a>

            @if($peminjamanAktif->timestamp_serah_terima && !$peminjamanAktif->timestamp_kembali)
                <form action="{{ route('peminjaman.kembalikan', $peminjamanAktif->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah selesai dinas dan ingin mengembalikan kendaraan?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Ajukan Pengembalian</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Editorial Metrics -->
<div class="grid grid-cols-3" style="margin-bottom: 2.25rem;">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Peminjaman Aktif</span>
            <div class="stat-value" style="color: var(--info);">{{ $peminjamanAktif ? 1 : 0 }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Sesi dinas berjalan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Menunggu Otorisasi</span>
            <div class="stat-value" style="color: var(--warning);">{{ $countPending }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Dalam proses telaah Manajer</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Permohonan Disetujui</span>
            <div class="stat-value" style="color: var(--success);">{{ $countApproved }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Total riwayat disetujui</span>
        </div>
    </div>
</div>

<!-- Kendaraan Tersedia -->
<div style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Armada Siap Digunakan</h2>
            <p style="color: var(--text-muted); font-size: 0.86rem; margin-top: 0.2rem;">Pilih salah satu armada dinas yang saat ini tersedia untuk tugas dinas luar kantor.</p>
        </div>
    </div>

    <div class="grid grid-cols-3">
        @forelse($kendaraanTersedia as $k)
        <div class="vehicle-card">
            <img src="{{ $k->foto_url }}" alt="{{ $k->nama_kendaraan }}" class="vehicle-img">
            <div class="vehicle-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.02rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.2rem;">{{ $k->nama_kendaraan }}</h3>
                        <span style="font-size: 0.8rem; color: var(--primary);">
                            <code>{{ $k->plat_nomor }}</code>
                        </span>
                    </div>
                    <span class="badge badge-tersedia"><span class="badge-dot"></span> Tersedia</span>
                </div>

                <div style="margin: 0.65rem 0; font-size: 0.82rem; color: var(--text-muted); display: flex; gap: 1rem;">
                    <span>Kapasitas: <strong style="color: var(--text-main);">{{ $k->kapasitas }} Penumpang</strong></span>
                    <span>Kode: <strong style="color: var(--text-main);">{{ $k->id_kendaraan }}</strong></span>
                </div>

                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <a href="{{ route('peminjaman.create', ['id_kendaraan' => $k->id_kendaraan]) }}" class="btn btn-primary btn-sm" style="width: 100%;">
                        <span>Pilih Unit Ini</span>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 2.5rem 1rem;">
            <p style="color: var(--text-subtle);">Saat ini seluruh armada dinas sedang dalam penggunaan atau perawatan berkala.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Riwayat Pengajuan Karyawan -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Riwayat Pengajuan Peminjaman Saya</h2>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary btn-sm">Lihat Seluruh Riwayat</a>
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
                    <td><code>{{ $trx->id_transaksi }}</code></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->kendaraan->nama_kendaraan ?? '-' }}</div>
                        <div style="font-size: 0.76rem; color: var(--primary);"><code>{{ $trx->kendaraan->plat_nomor ?? '-' }}</code></div>
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
                            <span class="badge badge-menunggu-acc"><span class="badge-dot"></span> ACC Pengembalian</span>
                        @else
                            <span class="badge badge-tersedia"><span class="badge-dot"></span> {{ $trx->status_sirkulasi }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('peminjaman.show', $trx->id_transaksi) }}" class="btn btn-secondary btn-sm" title="Lihat Tiket QR">
                            <span>Tiket</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.25rem 1rem; color: var(--text-subtle);">
                        Belum ada riwayat pengajuan peminjaman kendaraan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
