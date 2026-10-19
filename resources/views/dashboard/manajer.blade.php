@extends('layouts.app')

@section('title', 'Dashboard Manajer — SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Manajer Operasional</h1>
        <p class="page-subtitle">Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Tinjau dan otorisasi permohonan penggunaan kendaraan dinas operasional.</p>
    </div>
    <div>
        <a href="{{ route('otorisasi.index') }}" class="btn btn-secondary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <span>Seluruh Riwayat Otorisasi</span>
        </a>
    </div>
</div>

<!-- Editorial KPIs -->
<div class="grid grid-cols-4" style="margin-bottom: 2.25rem;">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Perlu Otorisasi</span>
            <div class="stat-value" style="color: var(--warning);">{{ $countPending }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Menunggu telaah persetujuan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Disetujui (Approved)</span>
            <div class="stat-value" style="color: var(--success);">{{ $countApproved }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Permohonan disetujui</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Ditolak (Rejected)</span>
            <div class="stat-value" style="color: var(--danger);">{{ $countRejected }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Permohonan tidak diizinkan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Total Permohonan</span>
            <div class="stat-value">{{ $totalTransaksi }}</div>
            <span style="font-size: 0.76rem; color: var(--text-subtle);">Akumulasi seluruh pengajuan</span>
        </div>
    </div>
</div>

<!-- Antrean Permohonan Menunggu Otorisasi -->
<div class="card" style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Antrean Permohonan Menunggu Persetujuan</h2>
            <p style="color: var(--text-muted); font-size: 0.86rem; margin-top: 0.2rem;">Permohonan di bawah ini memerlukan otorisasi manajer sebelum kendaraan dapat diserahterimakan oleh admin.</p>
        </div>
        <span class="badge badge-pending"><span class="badge-dot"></span> {{ $antreanOtorisasi->count() }} Permohonan</span>
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
                        <code>{{ $trx->id_transaksi }}</code>
                        <div style="font-size: 0.76rem; color: var(--text-subtle); margin-top: 0.2rem;">Diajukan: {{ $trx->tanggal_pengajuan->translatedFormat('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->pengguna->nama }}</div>
                        <div style="font-size: 0.76rem; color: var(--text-subtle);">NIK: {{ $trx->pengguna->username }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.76rem; color: var(--primary);">
                            <code>{{ $trx->kendaraan->plat_nomor }}</code> (Kapasitas: {{ $trx->kendaraan->kapasitas }} org)
                        </div>
                    </td>
                    <td style="max-width: 300px;">
                        <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.4;">{{ $trx->tujuan_operasional }}</p>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.5rem;">
                            <!-- Approve Button opens modal -->
                            <button type="button" class="btn btn-success btn-sm" onclick="openModal('modal-approve-{{ $trx->id_transaksi }}')">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Setujui</span>
                            </button>

                            <!-- Reject Button opens modal -->
                            <button type="button" class="btn btn-danger btn-sm" onclick="openModal('modal-reject-{{ $trx->id_transaksi }}')">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Tolak</span>
                            </button>
                        </div>

                        <!-- Modal Approve -->
                        <div id="modal-approve-{{ $trx->id_transaksi }}" class="modal-overlay">
                            <div class="modal-content" style="text-align: left;">
                                <div class="modal-header">
                                    <h3 style="font-size: 1.05rem; font-weight: 700;">Setujui Permohonan {{ $trx->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-approve-{{ $trx->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.approve', $trx->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1rem;">
                                            Anda akan menyetujui permohonan dinas <strong>{{ $trx->pengguna->nama }}</strong> untuk unit <strong>{{ $trx->kendaraan->nama_kendaraan }} ({{ $trx->kendaraan->plat_nomor }})</strong>.
                                        </p>
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_app_{{ $trx->id_transaksi }}">Catatan / Instruksi Manajer (Opsional):</label>
                                            <textarea id="catatan_app_{{ $trx->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3" placeholder="Contoh: Disetujui. Harap gunakan bahan bakar standar Pertamax & catat odometer.">Disetujui sesuai keperluan dinas operasional.</textarea>
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
                                    <h3 style="font-size: 1.05rem; font-weight: 700;">Tolak Permohonan {{ $trx->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-reject-{{ $trx->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.reject', $trx->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1rem;">
                                            Silakan berikan alasan penolakan permohonan kendaraan ini agar peminjam mendapatkan penjelasan yang transparan:
                                        </p>
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_rej_{{ $trx->id_transaksi }}">Alasan Penolakan (Wajib):</label>
                                            <textarea id="catatan_rej_{{ $trx->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3" placeholder="Contoh: Jadwal bertabrakan dengan agenda direksi / Armada sedang maintenance berkala." required></textarea>
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
                    <td colspan="5" style="text-align: center; padding: 2.25rem 1rem; color: var(--text-subtle);">
                        Tidak ada permohonan yang menunggu otorisasi saat ini. Seluruh permohonan telah diproses.
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
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Riwayat Keputusan Otorisasi Terbaru</h2>
        <a href="{{ route('otorisasi.index') }}" class="btn btn-secondary btn-sm">Lihat Seluruh Data</a>
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
                    <td><code>{{ $trx->id_transaksi }}</code></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->pengguna->nama }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $trx->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.76rem; color: var(--primary);"><code>{{ $trx->kendaraan->plat_nomor }}</code></div>
                    </td>
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
                    <td colspan="6" style="text-align: center; padding: 2.25rem 1rem; color: var(--text-subtle);">
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
