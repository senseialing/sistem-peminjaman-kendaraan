@extends('layouts.app')

@section('title', 'Edit Kendaraan ' . $kendaraan->id_kendaraan . ' - SPVMB')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Armada Kendaraan</h1>
            <p class="page-subtitle">Perbarui data unit kendaraan dinas operasional.</p>
        </div>
        <div>
            <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('kendaraan.update', $kendaraan->id_kendaraan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Kode Unit / Inventaris</label>
                    <input type="text" class="form-control" value="{{ $kendaraan->id_kendaraan }}" disabled style="background: var(--bg-elevated); cursor: not-allowed;">
                    <span class="form-hint">Primary Key (tidak dapat diubah)</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="plat_nomor">Nomor Polisi (Plat Nomor) <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="plat_nomor" name="plat_nomor" class="form-control" value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}" required>
                    @error('plat_nomor')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="nama_kendaraan">Nama / Merk Kendaraan <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nama_kendaraan" name="nama_kendaraan" class="form-control" value="{{ old('nama_kendaraan', $kendaraan->nama_kendaraan) }}" required>
                @error('nama_kendaraan')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="kapasitas">Kapasitas Maksimal (Penumpang) <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="kapasitas" name="kapasitas" class="form-control" value="{{ old('kapasitas', $kendaraan->kapasitas) }}" min="1" max="60" required>
                    @error('kapasitas')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="status_ketersediaan">Status Ketersediaan <span style="color: var(--danger);">*</span></label>
                    <select id="status_ketersediaan" name="status_ketersediaan" class="form-control" required>
                        <option value="Tersedia" {{ old('status_ketersediaan', $kendaraan->status_ketersediaan) === 'Tersedia' ? 'selected' : '' }}>Tersedia di Pool</option>
                        <option value="Sedang Dipinjam" {{ old('status_ketersediaan', $kendaraan->status_ketersediaan) === 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="foto_kendaraan">Ganti Foto Kendaraan</label>
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
                    <img src="{{ $kendaraan->foto_url }}" alt="Preview" style="width: 70px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                    <input type="file" id="foto_kendaraan" name="foto_kendaraan" class="form-control" accept="image/*">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
