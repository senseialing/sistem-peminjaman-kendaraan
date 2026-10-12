@extends('layouts.app')

@section('title', 'Detail & Label QR ' . $kendaraan->id_kendaraan . ' - SPVMB')

@section('content')
<div class="page-header no-print">
    <div>
        <h1 class="page-title">Detail Unit & Label QR Code</h1>
        <p class="page-subtitle">Informasi spesifikasi armada dan stiker barcode fisik inventaris kendaraan dinas.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary btn-sm">&larr; Daftar Armada</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            🖨️ Cetak Stiker Label QR
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 2rem; align-items: start;">
    <!-- Stiker Label QR Code Resmi (Designed for Print & Lamination) -->
    <div class="card" style="border: 2px dashed #94a3b8; text-align: center; background: var(--bg-card); padding: 2rem; border-radius: 18px; box-shadow: var(--shadow-md);">
        <div style="display: inline-block; padding: 0.25rem 0.75rem; background: #0f172a; color: #fff; font-size: 0.7rem; font-weight: 800; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">
            LABEL INVENTARIS KENDARAAN DINAS
        </div>

        <div style="margin: 0.5rem auto 1rem auto; display: inline-block; padding: 0.75rem; background: var(--bg-elevated); border-radius: 16px; border: 1px solid var(--border-color);">
            {!! $qrSvg !!}
        </div>

        <div style="margin-top: 0.5rem;">
            <div style="font-size: 1.4rem; font-weight: 800; font-family: var(--font-mono); color: var(--text-main); letter-spacing: 0.05em;">
                {{ $kendaraan->plat_nomor }}
            </div>
            <div style="font-size: 1rem; font-weight: 700; color: var(--primary); margin-top: 0.2rem;">
                {{ $kendaraan->nama_kendaraan }}
            </div>
            <div style="font-family: var(--font-mono); font-size: 0.82rem; color: var(--text-muted); margin-top: 0.25rem;">
                ID UNIT: {{ $kendaraan->id_kendaraan }} | ID QR: {{ $kendaraan->qrCode->id_qrcode ?? '-' }}
            </div>
        </div>

        <div style="margin-top: 1.25rem; font-size: 0.78rem; color: var(--text-subtle); border-top: 1px solid #f1f5f9; padding-top: 0.75rem;">
            Tempelkan stiker ini pada kaca depan bagian dalam atau dashboard mobil dinas untuk verifikasi scanner sirkulasi.
        </div>
    </div>

    <!-- Spesifikasi & Detail Unit -->
    <div class="card no-print">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">Spesifikasi Unit Kendaraan</h2>

        <div style="margin-bottom: 1.5rem; text-align: center;">
            <img src="{{ $kendaraan->foto_url }}" alt="{{ $kendaraan->nama_kendaraan }}" style="width: 100%; max-height: 200px; object-fit: contain; border-radius: 12px; background: var(--bg-elevated); border: 1px solid var(--border-color);">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: var(--bg-elevated); padding: 0.85rem 1rem; border-radius: 8px;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Kode Unit / PK:</span>
                <div style="font-weight: 700; font-family: var(--font-mono); font-size: 0.95rem;">{{ $kendaraan->id_kendaraan }}</div>
            </div>

            <div style="background: var(--bg-elevated); padding: 0.85rem 1rem; border-radius: 8px;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Nomor Polisi:</span>
                <div style="font-weight: 700; font-family: var(--font-mono); font-size: 0.95rem; color: var(--primary);">{{ $kendaraan->plat_nomor }}</div>
            </div>

            <div style="background: var(--bg-elevated); padding: 0.85rem 1rem; border-radius: 8px;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Kapasitas Penumpang:</span>
                <div style="font-weight: 700; font-size: 0.95rem;">👥 {{ $kendaraan->kapasitas }} Orang</div>
            </div>

            <div style="background: var(--bg-elevated); padding: 0.85rem 1rem; border-radius: 8px;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Status Saat Ini:</span>
                <div>
                    @if($kendaraan->status_ketersediaan === 'Tersedia')
                        <span class="badge badge-tersedia">Tersedia</span>
                    @else
                        <span class="badge badge-dipinjam">Sedang Dipinjam</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('kendaraan.edit', $kendaraan->id_kendaraan) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                ✏️ Edit Spesifikasi
            </a>
            <a href="{{ route('peminjaman.create', ['id_kendaraan' => $kendaraan->id_kendaraan]) }}" class="btn btn-primary btn-sm" style="flex: 1;">
                ➕ Buat Peminjaman
            </a>
        </div>
    </div>
</div>

<!-- Riwayat Peminjaman Unit Ini -->
<div class="card no-print" style="margin-top: 2rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">Riwayat Peminjaman Unit Ini</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Peminjam</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Otorisasi</th>
                    <th>Sirkulasi</th>
                    <th>Serah Terima</th>
                    <th>Pengembalian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraan->transaksi as $t)
                <tr>
                    <td><strong style="font-family: var(--font-mono);">{{ $t->id_transaksi }}</strong></td>
                    <td>{{ $t->pengguna->nama ?? '-' }}</td>
                    <td>{{ $t->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                    <td>
                        @if($t->status_otorisasi === 'Approved')
                            <span class="badge badge-approved">Approved</span>
                        @elseif($t->status_otorisasi === 'Rejected')
                            <span class="badge badge-rejected">Rejected</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td><span class="badge badge-tersedia">{{ $t->status_sirkulasi }}</span></td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                        {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                        {{ $t->timestamp_kembali ? $t->timestamp_kembali->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Belum ada riwayat transaksi untuk armada ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
