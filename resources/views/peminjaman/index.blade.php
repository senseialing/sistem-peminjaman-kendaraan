@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Saya - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Riwayat Pengajuan Peminjaman</h1>
        <p class="page-subtitle">Daftar seluruh rekam jejak peminjaman kendaraan dinas yang pernah Anda ajukan.</p>
    </div>
    <div>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            ➕ Buat Pengajuan Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Kendaraan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Keperluan / Tujuan</th>
                    <th>Otorisasi Manajer</th>
                    <th>Status Sirkulasi</th>
                    <th>Waktu Serah Terima</th>
                    <th>Waktu Kembali</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $t->id_transaksi }}</strong>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->kendaraan->nama_kendaraan ?? '-' }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">
                            {{ $t->kendaraan->plat_nomor ?? '-' }}
                        </div>
                    <td>
                        <div style="font-weight: 600;">{{ $t->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                        @if($t->tanggal_selesai && $t->tanggal_selesai->ne($t->tanggal_pengajuan))
                            <div style="font-size: 0.75rem; color: var(--primary);">s/d {{ $t->tanggal_selesai->translatedFormat('d M Y') }} ({{ $t->tanggal_pengajuan->diffInDays($t->tanggal_selesai) + 1 }} hari)</div>
                        @endif
                    </td>
                        {{ Str::limit($t->tujuan_operasional, 45) }}
                    </td>
                    <td>
                        @if($t->status_otorisasi === 'Approved')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui</span>
                        @elseif($t->status_otorisasi === 'Rejected')
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Ditolak</span>
                        @else
                            <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($t->status_sirkulasi === 'Menunggu ACC Pengembalian')
                            <span class="badge badge-menunggu-acc">ACC Pengembalian</span>
                        @else
                            <span class="badge badge-tersedia">{{ $t->status_sirkulasi }}</span>
                        @endif
                    </td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                        {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                        {{ $t->timestamp_kembali ? $t->timestamp_kembali->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('peminjaman.show', $t->id_transaksi) }}" class="btn btn-secondary btn-sm">
                            🎫 Tiket & QR
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Belum ada data pengajuan peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $transaksi->links() }}
    </div>
</div>
@endsection
