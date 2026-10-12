@extends('layouts.app')

@section('title', 'Sirkulasi Kunci & Kendaraan - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Sirkulasi Kunci & Kendaraan Operasional</h1>
        <p class="page-subtitle">Pusat kendali serah terima fisik kunci mobil dinas dan verifikasi penerimaan pengembalian armada.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('sirkulasi.scan') }}" class="btn btn-primary">
            🔍 Verifikasi / Scan QR
        </a>
    </div>
</div>

<!-- Navigation Tabs -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
    <a href="{{ route('sirkulasi.index', ['tab' => 'serah-terima']) }}" class="btn {{ $tab === 'serah-terima' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        🔑 Antrean Serah Terima Kunci ({{ $antreanSerahTerima->count() }})
    </a>
    <a href="{{ route('sirkulasi.index', ['tab' => 'pengembalian']) }}" class="btn {{ $tab === 'pengembalian' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        🔄 Antrean Pengembalian Armada ({{ $antreanPengembalian->count() }})
    </a>
    <a href="{{ route('sirkulasi.index', ['tab' => 'riwayat']) }}" class="btn {{ $tab === 'riwayat' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        📑 Riwayat Sirkulasi Selesai
    </a>
</div>

@if($tab === 'serah-terima')
<!-- Tab Serah Terima Kunci -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Permohonan Disetujui (Siap Check-Out Kunci)</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Karyawan telah mendapatkan approval dari manajer. Klik "Serahkan Kunci" saat fisik kunci mobil diserahkan.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Peminjam</th>
                    <th>Unit Kendaraan</th>
                    <th>Tanggal Dinas</th>
                    <th>Keperluan</th>
                    <th style="text-align: right;">Aksi Lapangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanSerahTerima as $t)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $t->id_transaksi }}</strong>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">NIK: {{ $t->pengguna->username }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">
                            {{ $t->kendaraan->plat_nomor }}
                        </div>
                    <td>
                        <div style="font-weight: 600;">{{ $t->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                        @if($t->tanggal_selesai && $t->tanggal_selesai->ne($t->tanggal_pengajuan))
                            <div style="font-size: 0.75rem; color: var(--primary);">s/d {{ $t->tanggal_selesai->translatedFormat('d M Y') }} ({{ $t->tanggal_pengajuan->diffInDays($t->tanggal_selesai) + 1 }} hr)</div>
                        @endif
                    </td>
                        {{ $t->tujuan_operasional }}
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.serah-terima', $t->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi serahkan kunci fisik mobil {{ $t->kendaraan->plat_nomor }} kepada {{ $t->pengguna->nama }}?')">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                🔑 Serahkan Kunci Sekarang
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Tidak ada antrean serah terima kunci. Seluruh kunci permohonan disetujui sudah diserahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif($tab === 'pengembalian')
<!-- Tab Pengembalian Armada -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Armada Sedang Dipinjam (Siap Check-In Kembali)</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Klik "Terima Kembali Kendaraan" setelah kunci fisik, STNK, dan unit kendaraan diperiksa di pool.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket & Waktu Serah Terima</th>
                    <th>Peminjam</th>
                    <th>Unit Kendaraan</th>
                    <th>Fase Sirkulasi</th>
                    <th>Keperluan</th>
                    <th style="text-align: right;">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanPengembalian as $t)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $t->id_transaksi }}</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                            Keluar: {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }} WIB
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">ID: {{ $t->pengguna->id_pengguna }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">
                            {{ $t->kendaraan->plat_nomor }}
                        </div>
                    </td>
                    <td>
                        @if($t->status_sirkulasi === 'Menunggu ACC Pengembalian')
                            <span class="badge badge-menunggu-acc">🔔 Pengembalian Diajukan</span>
                        @else
                            <span class="badge badge-dipinjam">Sedang Digunakan</span>
                        @endif
                    </td>
                    <td style="max-width: 200px; font-size: 0.85rem; color: var(--text-muted);">
                        {{ $t->tujuan_operasional }}
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('sirkulasi.pengembalian', $t->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Konfirmasi terima kembali kendaraan {{ $t->kendaraan->plat_nomor }}?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                🔄 Terima Kembali
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Semua kendaraan berada di pool. Tidak ada kendaraan yang sedang keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<!-- Tab Riwayat Sirkulasi Selesai -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Riwayat Sirkulasi Selesai</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Pencatatan otomatis timestamp serah terima & pengembalian kendaraan.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Peminjam</th>
                    <th>Unit Kendaraan</th>
                    <th>Waktu Serah Terima</th>
                    <th>Waktu Pengembalian</th>
                    <th>Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatSirkulasi as $t)
                <tr>
                    <td><strong style="font-family: var(--font-mono);">{{ $t->id_transaksi }}</strong></td>
                    <td>{{ $t->pengguna->nama }}</td>
                    <td>{{ $t->kendaraan->nama_kendaraan }} ({{ $t->kendaraan->plat_nomor }})</td>
                    <td style="font-size: 0.85rem; color: #059669;">
                        {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }} WIB
                    </td>
                    <td style="font-size: 0.85rem; color: var(--primary);">
                        {{ $t->timestamp_kembali ? $t->timestamp_kembali->translatedFormat('d M Y H:i') : '-' }} WIB
                    </td>
                    <td>
                        <span class="badge badge-approved">Selesai (Tersedia)</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Belum ada riwayat sirkulasi selesai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
