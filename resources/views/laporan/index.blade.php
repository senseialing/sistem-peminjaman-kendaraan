@extends('layouts.app')

@section('title', 'Laporan Sirkulasi & Audit Log - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Laporan Sirkulasi & Log Audit</h1>
        <p class="page-subtitle">Penarikan rekapitulasi data peminjaman dinas per periode dan rekam jejak cetak audit staf admin.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('laporan.cetak', ['mulai' => $mulai, 'akhir' => $akhir, 'jenis' => $jenis]) }}" target="_blank" class="btn btn-primary">
            🖨️ Cetak Dokumen Resmi & Catat Audit
        </a>
    </div>
</div>

<!-- Filter Form Card -->
<div class="card" style="margin-bottom: 2rem;">
    <form action="{{ route('laporan.index') }}" method="GET">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="mulai">Periode Mulai:</label>
                <input type="date" id="mulai" name="mulai" class="form-control" value="{{ $mulai }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="akhir">Periode Akhir:</label>
                <input type="date" id="akhir" name="akhir" class="form-control" value="{{ $akhir }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="jenis">Kategori Laporan:</label>
                <select id="jenis" name="jenis" class="form-control">
                    <option value="Bulanan" {{ $jenis === 'Bulanan' ? 'selected' : '' }}>Laporan Bulanan</option>
                    <option value="Tahunan" {{ $jenis === 'Tahunan' ? 'selected' : '' }}>Laporan Tahunan</option>
                    <option value="Operasional Khusus" {{ $jenis === 'Operasional Khusus' ? 'selected' : '' }}>Operasional Khusus</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%;">
                    🔍 Filter Data
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Preview Rekapitulasi Data -->
<div class="card" style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">
                Rekapitulasi Transaksi Periode: {{ \Carbon\Carbon::parse($mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y') }}
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Ditemukan <strong>{{ $transaksi->count() }} transaksi</strong> dalam rentang tanggal terpilih.</p>
        </div>
        <a href="{{ route('laporan.cetak', ['mulai' => $mulai, 'akhir' => $akhir, 'jenis' => $jenis]) }}" target="_blank" class="btn btn-primary btn-sm">
            🖨️ Preview Cetak PDF
        </a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Peminjam</th>
                    <th>Armada & Plat</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Otorisasi</th>
                    <th>Serah Terima</th>
                    <th>Pengembalian</th>
                    <th>Keperluan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td><strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $t->id_transaksi }}</strong></td>
                    <td>{{ $t->pengguna->nama }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">{{ $t->kendaraan->plat_nomor }}</div>
                    </td>
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
                    <td style="font-size: 0.82rem; color: #059669;">
                        {{ $t->timestamp_serah_terima ? $t->timestamp_serah_terima->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                    <td style="font-size: 0.82rem; color: var(--primary);">
                        {{ $t->timestamp_kembali ? $t->timestamp_kembali->translatedFormat('d M Y H:i') : '-' }}
                    </td>
                    <td style="max-width: 220px; font-size: 0.82rem; color: var(--text-muted);" title="{{ $t->tujuan_operasional }}">
                        {{ Str::limit($t->tujuan_operasional, 40) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Tidak ada transaksi peminjaman pada rentang tanggal terpilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Log Audit Cetak Laporan (Tabel 5 Spesifikasi) -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Rekam Jejak Cetak Laporan (Audit Internal)</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Tabel <code>laporan_sirkulasi</code> — Log riwayat penarikan dan pencetakan berkas oleh Staf Admin.</p>
        </div>
        <span class="badge badge-tersedia">Tabel: laporan_sirkulasi</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID Laporan (PK)</th>
                    <th>Rentang Periode Mulai</th>
                    <th>Rentang Periode Akhir</th>
                    <th>Jenis Laporan</th>
                    <th>Total Transaksi Tertera</th>
                    <th>Staf Admin Pencetak</th>
                    <th>Waktu Tercatat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--primary);">{{ $log->id_laporan }}</strong>
                    </td>
                    <td>{{ $log->rentang_periode_mulai->translatedFormat('d M Y') }}</td>
                    <td>{{ $log->rentang_periode_akhir->translatedFormat('d M Y') }}</td>
                    <td>
                        <span style="font-weight: 600; background: var(--bg-elevated); padding: 2px 8px; border-radius: 4px; font-size: 0.85rem;">
                            {{ $log->jenis_laporan }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $log->total_transaksi }} Data</strong>
                    </td>
                    <td>
                        {{ $log->stafAdmin->nama ?? ($log->id_staf_admin ?? 'Admin Sistem') }}
                    </td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                        {{ $log->created_at->translatedFormat('d M Y H:i:s') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Belum ada riwayat cetak laporan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
