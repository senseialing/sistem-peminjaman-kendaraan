@extends('layouts.app')

@section('title', 'Manajemen Otorisasi Peminjaman - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Otorisasi Permohonan Peminjaman</h1>
        <p class="page-subtitle">Daftar seluruh pengajuan kendaraan dinas operasional untuk ditinjau oleh Manajer.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('otorisasi.index') }}" class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Semua</a>
        <a href="{{ route('otorisasi.index', ['status' => 'pending']) }}" class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Pending</a>
        <a href="{{ route('otorisasi.index', ['status' => 'approved']) }}" class="btn {{ $status === 'approved' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Disetujui</a>
        <a href="{{ route('otorisasi.index', ['status' => 'rejected']) }}" class="btn {{ $status === 'rejected' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Ditolak</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiket & Tanggal</th>
                    <th>Pemohon</th>
                    <th>Armada Kendaraan</th>
                    <th>Keperluan Dinas</th>
                    <th>Status Keputusan</th>
                    <th>Catatan Manajer</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $t->id_transaksi }}</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                            {{ $t->tanggal_pengajuan->translatedFormat('d M Y') }}
                            @if($t->tanggal_selesai && $t->tanggal_selesai->ne($t->tanggal_pengajuan))
                                - {{ $t->tanggal_selesai->translatedFormat('d M Y') }} ({{ $t->tanggal_pengajuan->diffInDays($t->tanggal_selesai) + 1 }} hr)
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->pengguna->nama }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">NIK: {{ $t->pengguna->username }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $t->kendaraan->nama_kendaraan }}</div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-family: var(--font-mono);">{{ $t->kendaraan->plat_nomor }}</div>
                    </td>
                    <td style="max-width: 250px; font-size: 0.85rem; color: #334155;">
                        {{ $t->tujuan_operasional }}
                    </td>
                    <td>
                        @if($t->status_otorisasi === 'Approved')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Disetujui</span>
                        @elseif($t->status_otorisasi === 'Rejected')
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Ditolak</span>
                        @else
                            <span class="badge badge-pending"><span class="badge-dot"></span> Menunggu</span>
                        @endif
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $t->catatan_manajer ?: '-' }}
                    </td>
                    <td style="text-align: right;">
                        @if($t->status_otorisasi === 'Pending')
                        <div style="display: inline-flex; gap: 0.4rem;">
                            <button type="button" class="btn btn-success btn-sm" onclick="openModal('modal-app-{{ $t->id_transaksi }}')">
                                Setujui
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openModal('modal-rej-{{ $t->id_transaksi }}')">
                                Tolak
                            </button>
                        </div>

                        <!-- Modal Approve -->
                        <div id="modal-app-{{ $t->id_transaksi }}" class="modal-overlay">
                            <div class="modal-content" style="text-align: left;">
                                <div class="modal-header">
                                    <h3 style="font-size: 1.1rem; font-weight: 700;">Setujui {{ $t->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-app-{{ $t->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.approve', $t->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                                            Setujui peminjaman kendaraan <strong>{{ $t->kendaraan->nama_kendaraan }} ({{ $t->kendaraan->plat_nomor }})</strong> oleh <strong>{{ $t->pengguna->nama }}</strong>.
                                        </p>
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_app_idx_{{ $t->id_transaksi }}">Instruksi / Catatan Manajer:</label>
                                            <textarea id="catatan_app_idx_{{ $t->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3">Disetujui untuk dinas operasional.</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-app-{{ $t->id_transaksi }}')">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm">Setujui Permohonan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Reject -->
                        <div id="modal-rej-{{ $t->id_transaksi }}" class="modal-overlay">
                            <div class="modal-content" style="text-align: left;">
                                <div class="modal-header">
                                    <h3 style="font-size: 1.1rem; font-weight: 700;">Tolak {{ $t->id_transaksi }}</h3>
                                    <button type="button" onclick="closeModal('modal-rej-{{ $t->id_transaksi }}')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
                                </div>
                                <form action="{{ route('otorisasi.reject', $t->id_transaksi) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="form-label" for="catatan_rej_idx_{{ $t->id_transaksi }}">Alasan Penolakan:</label>
                                            <textarea id="catatan_rej_idx_{{ $t->id_transaksi }}" name="catatan_manajer" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-rej-{{ $t->id_transaksi }}')">Batal</button>
                                        <button type="submit" class="btn btn-danger btn-sm">Tolak Permohonan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                            <a href="{{ route('peminjaman.show', $t->id_transaksi) }}" class="btn btn-secondary btn-sm">
                                Detail Tiket
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Tidak ada data permohonan yang sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $transaksi->appends(['status' => $status])->links() }}
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
