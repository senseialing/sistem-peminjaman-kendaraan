@extends('layouts.app')

@section('title', 'Dashboard Manajer - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Manajer Operasional</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Tinjau dan otorisasi permohonan penggunaan kendaraan dinas operasional.</p>
    </div>
    <div>
        <a href="{{ route('otorisasi.index') }}" class="btn btn-secondary">
            📑 Seluruh Riwayat Otorisasi
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon amber">⏳</div>
        <div class="stat-info">
            <div class="stat-value">{{ $countPending }}</div>
            <div class="stat-label">Perlu Otorisasi</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $countApproved }}</div>
            <div class="stat-label">Disetujui (Approved)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">❌</div>
        <div class="stat-info">
            <div class="stat-value">{{ $countRejected }}</div>
            <div class="stat-label">Ditolak (Rejected)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">📊</div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalTransaksi }}</div>
            <div class="stat-label">Total Permohonan</div>
        </div>
    </div>
</div>

<!-- Antrean Permohonan Menunggu Otorisasi -->
<div class="card" style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">Antrean Permohonan Menunggu Persetujuan</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Permohonan di bawah ini memerlukan otorisasi manajer sebelum kendaraan dapat diserahterimakan oleh admin.</p>
        </div>
        <span class="badge badge-pending">{{ $antreanOtorisasi->count() }} Permohonan</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket & Tanggal</th>
                    <th>Peminjam</th>
                    <th>Kendaraan Diajukan</th>
                    <th>Keperluan / Tujuan Dinas</th>
                    <th style="text-align: right;">Aksi Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanOtorisasi as $trx)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $trx->id_transaksi }}</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">Diajukan: {{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">NIK: {{ $trx->pengguna->username }} ({{ $trx->id_pengguna }})</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">
                            {{ $trx->kendaraan->plat_nomor }} (Kapasitas: {{ $trx->kendaraan->kapasitas }} org)
                        </div>
                    </td>
                    <td style="max-width: 300px;">
                        <p style="font-size: 0.88rem; color: #334155;">{{ $trx->tujuan_operasional }}</p>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.5rem;">
                            <!-- Approve Button opens modal -->
                            <button type="button" class="btn btn-success btn-sm" onclick="openModal('modal-approve-{{ $trx->id_transaksi }}')">
                                ✅ Setujui
                            </button>

                            <!-- Reject Button opens modal -->
                            <button type="button" class="btn btn-danger btn-sm" onclick="openModal('modal-reject-{{ $trx->id_transaksi }}')">
                                ❌ Tolak
                            </button>
                        </div>

                        <!-- Modal Approve -->
                        <div id="modal-approve-{{ $trx->id_transaksi }}" class="modal-overlay">
                            <div class="modal-content" style="text-align: left;">
                                <div class="modal-header">
                                    <h3 style="font-size: 1.1rem; font-weight: 700;">Setujui Permohonan {{ $trx->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-approve-{{ $trx->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.approve', $trx->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                                            Anda akan menyetujui permohonan <strong>{{ $trx->pengguna->nama }}</strong> untuk unit <strong>{{ $trx->kendaraan->nama_kendaraan }} ({{ $trx->kendaraan->plat_nomor }})</strong>.
                                        </p>
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_app_{{ $trx->id_transaksi }}">Catatan / Instruksi Manajer (Opsional):</label>
                                            <textarea id="catatan_app_{{ $trx->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3" placeholder="Contoh: Disetujui. Harap gunakan bahan bakar standar Pertamax & catat odometer.">Disetujui sesuai keperluan dinas.</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-approve-{{ $trx->id_transaksi }}')">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm">Konfirmasi Persetujuan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Reject -->
                        <div id="modal-reject-{{ $trx->id_transaksi }}" class="modal-overlay">
                            <div class="modal-content" style="text-align: left;">
                                <div class="modal-header">
                                    <h3 style="font-size: 1.1rem; font-weight: 700;">Tolak Permohonan {{ $trx->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-reject-{{ $trx->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.reject', $trx->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                                            Silakan berikan alasan penolakan permohonan kendaraan ini agar peminjam mendapatkan penjelasan yang jelas:
                                        </p>
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_rej_{{ $trx->id_transaksi }}">Alasan Penolakan (Wajib):</label>
                                            <textarea id="catatan_rej_{{ $trx->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3" placeholder="Contoh: Jadwal bertabrakan dengan agenda direksi / Armada sedang maintenance rutin." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-reject-{{ $trx->id_transaksi }}')">Batal</button>
                                        <button type="submit" class="btn btn-danger btn-sm">Tolak Permohonan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        🎉 Tidak ada permohonan yang menunggu otorisasi saat ini. Seluruh permohonan telah diproses.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Keputusan Otorisasi Terkini -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700;">Riwayat Keputusan Otorisasi Terbaru</h2>
        <a href="{{ route('otorisasi.index') }}" class="btn btn-secondary btn-sm">Lihat Semua Data &rarr;</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Peminjam</th>
                    <th>Kendaraan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status Keputusan</th>
                    <th>Catatan Manajer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatOtorisasi as $trx)
                <tr>
                    <td><strong style="font-family: var(--font-mono);">{{ $trx->id_transaksi }}</strong></td>
                    <td>{{ $trx->pengguna->nama }}</td>
                    <td>{{ $trx->kendaraan->nama_kendaraan }} ({{ $trx->kendaraan->plat_nomor }})</td>
                    <td>{{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                    <td>
                        @if($trx->status_otorisasi === 'Approved')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui</span>
                        @else
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Ditolak</span>
                        @endif
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;">
                        {{ $trx->catatan_manajer ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Belum ada riwayat otorisasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
</script>
@endsection
