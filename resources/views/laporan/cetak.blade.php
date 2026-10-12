<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Sirkulasi Kendaraan Dinas - {{ $idLaporan }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <style>
        body {
            background-color: #f1f5f9;
            color: #000;
            padding: 2rem 1rem;
        }
        .paper-a4 {
            background: var(--bg-card);
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 3.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 1.25rem;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }
        .kop-logo {
            font-size: 3rem;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h2 {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin-bottom: 0.2rem;
        }
        .kop-text h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }
        .kop-text p {
            font-size: 0.8rem;
            color: #334155;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .doc-title h3 {
            font-size: 1.15rem;
            font-weight: 800;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .meta-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }
        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            margin-bottom: 2.5rem;
        }
        .table-laporan th, .table-laporan td {
            border: 1px solid #334155;
            padding: 0.55rem 0.65rem;
            vertical-align: top;
        }
        .table-laporan th {
            background: var(--bg-elevated);
            font-weight: 700;
            text-align: center;
        }
        .ttd-box {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding: 0 2rem;
        }
        .ttd-col {
            text-align: center;
            width: 220px;
            font-size: 0.85rem;
        }
        .ttd-space {
            height: 70px;
        }
        @media print {
            body {
                background: var(--bg-card) !important;
                padding: 0 !important;
            }
            .paper-a4 {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 900px; margin: 0 auto 1rem auto; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Panel Laporan</a>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            🖨️ Cetak Dokumen / Simpan PDF
        </button>
    </div>
</div>

<div class="paper-a4">
    <!-- Kop Surat Resmi -->
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ asset('images/logo-spv.png') }}" alt="Logo SPV" style="height: 65px; width: auto; object-fit: contain;">
        </div>
        <div class="kop-text">
            <h2>PT. SARANA PRIMA VELOCITY (SPV)</h2>
            <h4>DIVISI LOGISTIK & PENGELOLAAN ARMADA KENDARAAN (SPVMB)</h4>
            <p>SPV Tower Lt. 12, Jl. Jend. Gatot Subroto Kav. 18, Jakarta Selatan | Telp: (021) 798-2000 | fleet@spv.co.id</p>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h3>REKAPITULASI LAPORAN SIRKULASI KENDARAAN DINAS</h3>
        <span style="font-size: 0.85rem; font-family: monospace;">Nomor Registrasi Audit: {{ $idLaporan }}</span>
    </div>

    <!-- Meta Data -->
    <div class="meta-box">
        <div>
            <div><strong>Rentang Periode:</strong> {{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($akhir)->translatedFormat('d F Y') }}</div>
            <div><strong>Kategori Laporan:</strong> {{ $jenis }}</div>
            <div><strong>Total Transaksi:</strong> {{ $transaksi->count() }} Peminjaman</div>
        </div>
        <div style="text-align: right;">
            <div><strong>Tanggal Dicetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
            <div><strong>Petugas Pencetak:</strong> {{ Auth::user()->nama }} ({{ Auth::user()->id_pengguna }})</div>
            <div><strong>Status Dokumen:</strong> <span style="color: #059669; font-weight: 700;">TERVERIFIKASI AUDIT</span></div>
        </div>
    </div>

    <!-- Tabel Data Rekapitulasi -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Tiket</th>
                <th>Pegawai Peminjam</th>
                <th>Unit & Plat Nomor</th>
                <th>Tgl Dinas</th>
                <th>Serah Terima Kunci</th>
                <th>Pengembalian</th>
                <th>Keperluan Dinas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: 700;">{{ $t->id_transaksi }}</td>
                <td>
                    <strong>{{ $t->pengguna->nama }}</strong>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $t->id_pengguna }}</div>
                </td>
                <td>
                    {{ $t->kendaraan->nama_kendaraan }}
                    <div style="font-weight: 700; font-family: monospace;">{{ $t->kendaraan->plat_nomor }}</div>
                </td>
                <td style="white-space: nowrap; font-size: 0.8rem;">
                    {{ $t->tanggal_pengajuan->translatedFormat('d/m/Y') }}
                    @if($t->tanggal_selesai && $t->tanggal_selesai->ne($t->tanggal_pengajuan))
                        <br><span style="font-size: 0.72rem; color: #64748b;">s/d {{ $t->tanggal_selesai->translatedFormat('d/m/Y') }} ({{ $t->tanggal_pengajuan->diffInDays($t->tanggal_selesai) + 1 }} hr)</span>
                    @endif
                </td>
                <td style="font-size: 0.75rem;">
                    {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->format('d/m/Y H:i') : '-' }}
                </td>
                <td style="font-size: 0.75rem;">
                    {{ $t->timestamp_kembali ? $t->timestamp_kembali->format('d/m/Y H:i') : '-' }}
                </td>
                <td style="font-size: 0.75rem;">
                    {{ $t->tujuan_operasional }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem;">
                    Tidak ada data transaksi peminjaman pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi -->
    <div class="ttd-box">
        <div class="ttd-col">
            <div>Dibuat oleh,</div>
            <div style="font-weight: 600; margin-top: 0.2rem;">Staf Admin Pengelola Armada</div>
            <div class="ttd-space"></div>
            <div style="font-weight: 700; text-decoration: underline;">{{ Auth::user()->nama }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">NIK/ID: {{ Auth::user()->username }}</div>
        </div>

        <div class="ttd-col">
            <div>Mengetahui & Menyetujui,</div>
            <div style="font-weight: 600; margin-top: 0.2rem;">Manajer Operasional & GA</div>
            <div class="ttd-space"></div>
            <div style="font-weight: 700; text-decoration: underline;">Hendra Wijaya, S.T.</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">NIP: 198204152005011002</div>
        </div>
    </div>
</div>

</body>
</html>
