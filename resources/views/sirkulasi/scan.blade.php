@extends('layouts.app')

@section('title', 'Verifikasi & Scan QR Code - SPVMB')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Verifikasi & Scanner Lapangan</h1>
            <p class="page-subtitle">Pindai QR code tiket atau masukkan nomor plat / ID tiket untuk verifikasi serah terima & pengembalian seketika.</p>
        </div>
        <div>
            <a href="{{ route('sirkulasi.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Sirkulasi</a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <form action="{{ route('sirkulasi.scan') }}" method="GET">
            <div class="form-group">
                <label class="form-label" for="keyword">Input Data Matriks QR / No. Tiket / Plat Nomor:</label>
                <div style="display: flex; gap: 0.75rem;">
                    <input type="text" id="keyword" name="keyword" class="form-control" value="{{ $keyword }}" placeholder="Ketik nomor tiket (TRX-...), kode unit (KND-...), atau plat nomor..." autofocus required>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                        🔍 Cari & Verifikasi
                    </button>
                </div>
            </div>

            <!-- Quick Paste Helpers -->
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; font-size: 0.8rem; color: var(--text-muted);">
                <span>Coba simulasi data:</span>
                <a href="{{ route('sirkulasi.scan', ['keyword' => 'TRX-202610-0001']) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 2px 8px;">
                    Tiket: TRX-202610-0001
                </a>
                <a href="{{ route('sirkulasi.scan', ['keyword' => 'TRX-202610-0002']) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 2px 8px;">
                    Tiket: TRX-202610-0002
                </a>
                <a href="{{ route('sirkulasi.scan', ['keyword' => 'B 1928 KDA']) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 2px 8px;">
                    Plat: B 1928 KDA
                </a>
            </div>
        </form>
    </div>

    @if($keyword)
        @if($hasil)
            <div class="card" style="border: 2px solid #3b82f6; box-shadow: var(--shadow-lg);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase;">Hasil Verifikasi Ditemukan</span>
                        <h2 style="font-size: 1.4rem; font-weight: 800; font-family: var(--font-mono); margin-top: 0.2rem;">
                            {{ $hasil->id_transaksi }}
                        </h2>
                    </div>
                    <div>
                        @if($hasil->status_otorisasi === 'Approved')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui Manajer</span>
                        @elseif($hasil->status_otorisasi === 'Rejected')
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Ditolak</span>
                        @else
                            <span class="badge badge-pending"><span class="badge-dot"></span> Menunggu Persetujuan</span>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; background: var(--bg-elevated); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Pegawai Peminjam:</span>
                        <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);">{{ $hasil->pengguna->nama }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">NIK: {{ $hasil->pengguna->username }}</div>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Armada Unit:</span>
                        <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);">{{ $hasil->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.85rem; color: var(--primary); font-family: var(--font-mono); font-weight: 700;">{{ $hasil->kendaraan->plat_nomor }}</div>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Status Sirkulasi:</span>
                        <div>
                            @if($hasil->status_sirkulasi === 'Menunggu ACC Pengembalian')
                                <span class="badge badge-menunggu-acc">ACC Pengembalian</span>
                            @else
                                <span class="badge badge-tersedia">{{ $hasil->status_sirkulasi }}</span>
                            @endif
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Kendaraan: <strong>{{ $hasil->kendaraan->status_ketersediaan }}</strong>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">Keperluan Dinas:</span>
                    <p style="font-size: 0.9rem; color: #334155; margin-top: 0.25rem;">{{ $hasil->tujuan_operasional }}</p>
                </div>

                <!-- Aksi Eksekusi Lapangan -->
                <div style="padding-top: 1.25rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                    <a href="{{ route('peminjaman.show', $hasil->id_transaksi) }}" class="btn btn-secondary btn-sm">
                        🎫 Buka Tiket Lengkap
                    </a>

                    <div style="display: flex; gap: 0.5rem;">
                        @if($hasil->status_otorisasi === 'Approved' && !$hasil->timestamp_serah_terima)
                            <form action="{{ route('sirkulasi.serah-terima', $hasil->id_transaksi) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    🔑 Eksekusi Serah Terima Kunci
                                </button>
                            </form>
                        @elseif($hasil->timestamp_serah_terima && !$hasil->timestamp_kembali)
                            <form action="{{ route('sirkulasi.pengembalian', $hasil->id_transaksi) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    🔄 Eksekusi Penerimaan Pengembalian
                                </button>
                            </form>
                        @else
                            <span style="font-size: 0.88rem; color: #059669; font-weight: 600;">
                                ✅ Transaksi ini telah tuntas diserahterimakan dan dikembalikan.
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
                <h3 style="font-size: 1.15rem; font-weight: 700;">Data Tidak Ditemukan</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                    Tidak ditemukan tiket peminjaman atau armada yang cocok dengan kata kunci: <code>{{ $keyword }}</code>
                </p>
            </div>
        @endif
    @endif
</div>
@endsection
