@extends('layouts.app')

@section('title', 'Tiket Peminjaman ' . $transaksi->id_transaksi . ' - SPVMB')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div class="page-header no-print">
        <div>
            <h1 class="page-title">Tiket Peminjaman Digital</h1>
            <p class="page-subtitle">Bukti resmi pengajuan & serah terima kendaraan dinas operasional.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary btn-sm">&larr; Riwayat</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ Cetak Tiket
            </button>
        </div>
    </div>

    <!-- Official Ticket Card -->
    <div class="ticket-card">
        <div class="ticket-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-subtle); font-weight: 700;">
                        SURAT JALAN & TIKET OPERASIONAL
                    </span>
                    <h2 style="font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; margin-top: 0.2rem;">
                        {{ $transaksi->id_transaksi }}
                    </h2>
                    <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 0.2rem;">
                        SPVMB — Sistem Pengelolaan Armada Kendaraan
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-subtle); margin-bottom: 0.3rem;">Status Otorisasi:</div>
                    @if($transaksi->status_otorisasi === 'Approved')
                        <span class="badge badge-approved" style="font-size: 0.85rem; padding: 0.35rem 0.85rem;">
                            <span class="badge-dot"></span> DISETUJUI MANAJER
                        </span>
                    @elseif($transaksi->status_otorisasi === 'Rejected')
                        <span class="badge badge-rejected" style="font-size: 0.85rem; padding: 0.35rem 0.85rem;">
                            <span class="badge-dot"></span> PERMOHONAN DITOLAK
                        </span>
                    @else
                        <span class="badge badge-pending" style="font-size: 0.85rem; padding: 0.35rem 0.85rem;">
                            <span class="badge-dot"></span> MENUNGGU PERSETUJUAN
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ticket-divider">
            <div class="ticket-divider-cut-left"></div>
            <div class="ticket-divider-cut-right"></div>
        </div>

        <div class="ticket-body">
            <!-- QR Code & Quick Scan Info -->
            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-elevated); border: 1px dashed #cbd5e1; border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1.25rem;">
                <div style="flex: 1; min-width: 200px;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                        QR Code Validasi Lapangan
                    </div>
                    <p style="font-size: 0.88rem; color: #334155; margin-top: 0.25rem;">
                        Tunjukkan QR Code ini kepada <strong>Staf Admin Operasional</strong> saat pengambilan kunci fisik kendaraan atau konfirmasi pengembalian.
                    </p>
                    <div style="margin-top: 0.5rem; font-family: var(--font-mono); font-size: 0.75rem; color: var(--text-muted); word-break: break-all;">
                        TOKEN: {{ md5($transaksi->id_transaksi) }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; background: var(--bg-card); padding: 0.5rem; border-radius: 12px; box-shadow: var(--shadow-sm);">
                    {!! $qrSvg !!}
                    <span style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem; font-weight: 600;">SCAN VERIFIKASI</span>
                </div>
            </div>

            <!-- Detail Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Peminjam Info -->
                <div style="border-left: 3px solid #3b82f6; padding-left: 1rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Data Pegawai / Peminjam</span>
                    <div style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-top: 0.2rem;">
                        {{ $transaksi->pengguna->nama }}
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        ID Pegawai: <strong>{{ $transaksi->pengguna->id_pengguna }}</strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Username/NIK: {{ $transaksi->pengguna->username }}
                    </div>
                </div>

                <!-- Kendaraan Info -->
                <div style="border-left: 3px solid #10b981; padding-left: 1rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Armada Kendaraan Dinas</span>
                    <div style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-top: 0.2rem;">
                        {{ $transaksi->kendaraan->nama_kendaraan }}
                    </div>
                    <div style="font-size: 0.88rem; font-family: var(--font-mono); font-weight: 700; color: var(--primary);">
                        {{ $transaksi->kendaraan->plat_nomor }}
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Kode Unit: {{ $transaksi->kendaraan->id_kendaraan }} | Kapasitas: {{ $transaksi->kendaraan->kapasitas }} Org
                    </div>
                </div>
            </div>

            <!-- Sirkulasi & Tanggal -->
            <div style="background: var(--bg-elevated); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Periode Penggunaan Dinas:</div>
                        <div style="font-weight: 700; font-size: 0.95rem; margin-top: 0.15rem; color: var(--text-main);">
                            @if($transaksi->tanggal_selesai && $transaksi->tanggal_selesai->ne($transaksi->tanggal_pengajuan))
                                {{ $transaksi->tanggal_pengajuan->translatedFormat('d M Y') }} s/d {{ $transaksi->tanggal_selesai->translatedFormat('d M Y') }}
                                <div style="font-size: 0.78rem; color: var(--primary); font-weight: 600;">
                                    ({{ $transaksi->tanggal_pengajuan->diffInDays($transaksi->tanggal_selesai) + 1 }} Hari)
                                </div>
                            @else
                                {{ $transaksi->tanggal_pengajuan->translatedFormat('l, d F Y') }} (1 Hari)
                            @endif
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Waktu Serah Terima Kunci:</div>
                        <div style="font-weight: 700; font-size: 0.95rem; margin-top: 0.15rem; color: {{ $transaksi->timestamp_serah_terima ? '#22c55e' : '#f59e0b' }};">
                            {{ $transaksi->timestamp_serah_terima ? $transaksi->timestamp_serah_terima->translatedFormat('d M Y H:i') . ' WIB' : 'Belum Diserahterimakan' }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Waktu Pengembalian:</div>
                        <div style="font-weight: 700; font-size: 0.95rem; margin-top: 0.15rem; color: {{ $transaksi->timestamp_kembali ? '#22c55e' : '#a0a0a0' }};">
                            {{ $transaksi->timestamp_kembali ? $transaksi->timestamp_kembali->translatedFormat('d M Y H:i') . ' WIB' : 'Belum Dikembalikan' }}
                        </div>
                    </div>
            </div>

            <!-- Keperluan Operasional -->
            <div style="margin-bottom: 1.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Keperluan / Tujuan Operasional:</span>
                <p style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; font-size: 0.92rem; color: var(--text-main); margin-top: 0.35rem; line-height: 1.5;">
                    {{ $transaksi->tujuan_operasional }}
                </p>
            </div>

            <!-- Catatan Manajer -->
            @if($transaksi->catatan_manajer)
            <div style="margin-bottom: 1.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #f59e0b;">Catatan / Disposisi Manajer:</span>
                <p style="font-size: 0.9rem; color: #fde68a; margin-top: 0.25rem;">
                    {{ $transaksi->catatan_manajer }}
                </p>
            </div>
            @endif

            <!-- Action: Return Vehicle if ongoing -->
            @if($transaksi->status_otorisasi === 'Approved' && $transaksi->timestamp_serah_terima && !$transaksi->timestamp_kembali)
            <div class="no-print" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <strong style="color: var(--text-main);">Sudah selesai menggunakan kendaraan dinas?</strong>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Klik tombol untuk mengajukan proses pengembalian kepada staf admin.</div>
                </div>

                <form action="{{ route('peminjaman.kembalikan', $transaksi->id_transaksi) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian unit kendaraan?')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        🔄 Selesai & Ajukan Pengembalian
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
