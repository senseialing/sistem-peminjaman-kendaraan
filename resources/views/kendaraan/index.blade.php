@extends('layouts.app')

@section('title', 'Manajemen Armada & QR Code - SPVMB')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Armada Kendaraan Dinas</h1>
        <p class="page-subtitle">Inventaris unit kendaraan operasional, kapasitas, status ketersediaan, dan label QR Code fisik.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
            ➕ Tambah Unit Kendaraan
        </a>
    </div>
</div>

<!-- Filter Tabs -->
<div style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem;">
    <a href="{{ route('kendaraan.index') }}" class="btn {{ empty($status) ? 'btn-primary' : 'btn-secondary' }} btn-sm">Semua Armada</a>
    <a href="{{ route('kendaraan.index', ['status' => 'Tersedia']) }}" class="btn {{ $status === 'Tersedia' ? 'btn-primary' : 'btn-secondary' }} btn-sm">🟢 Tersedia di Pool</a>
    <a href="{{ route('kendaraan.index', ['status' => 'Sedang Dipinjam']) }}" class="btn {{ $status === 'Sedang Dipinjam' ? 'btn-primary' : 'btn-secondary' }} btn-sm">🔵 Sedang Dipinjam</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Foto & Unit</th>
                    <th>Kode Inventaris</th>
                    <th>Nomor Polisi (Plat)</th>
                    <th>Kapasitas</th>
                    <th>Status Ketersediaan</th>
                    <th>QR Code Matriks</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraans as $k)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <img src="{{ $k->foto_url }}" alt="{{ $k->nama_kendaraan }}" style="width: 54px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                            <div>
                                <strong style="font-size: 0.95rem;">{{ $k->nama_kendaraan }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong style="font-family: var(--font-mono); color: var(--text-main);">{{ $k->id_kendaraan }}</strong>
                    </td>
                    <td>
                        <span style="font-family: var(--font-mono); font-weight: 700; color: var(--primary); background: rgba(225, 29, 45, 0.12); padding: 3px 8px; border-radius: 4px; border: 1px solid rgba(225, 29, 45, 0.3);">
                            {{ $k->plat_nomor }}
                        </span>
                    </td>
                    <td>
                        <span>👥 {{ $k->kapasitas }} Orang</span>
                    </td>
                    <td>
                        @if($k->status_ketersediaan === 'Tersedia')
                            <span class="badge badge-tersedia"><span class="badge-dot"></span> Tersedia</span>
                        @else
                            <span class="badge badge-dipinjam"><span class="badge-dot"></span> Sedang Dipinjam</span>
                        @endif
                    </td>
                    <td>
                        @if($k->qrCode)
                            <a href="{{ route('kendaraan.show', $k->id_kendaraan) }}" title="Lihat Label QR Code">
                                <span style="font-family: var(--font-mono); font-size: 0.78rem; color: var(--text-muted); background: var(--bg-elevated); padding: 3px 6px; border-radius: 4px; border: 1px solid var(--border-color);">
                                    🏷️ {{ $k->qrCode->id_qrcode }}
                                </span>
                            </a>
                        @else
                            <span style="color: var(--text-subtle); font-size: 0.8rem;">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.4rem;">
                            <a href="{{ route('kendaraan.show', $k->id_kendaraan) }}" class="btn btn-secondary btn-sm" title="Label Stiker QR">
                                🖨️ Label QR
                            </a>
                            <a href="{{ route('kendaraan.edit', $k->id_kendaraan) }}" class="btn btn-secondary btn-sm" title="Edit Unit">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('kendaraan.destroy', $k->id_kendaraan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kendaraan ini?')" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        Belum ada unit kendaraan dinas yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $kendaraans->links() }}
    </div>
</div>
@endsection
