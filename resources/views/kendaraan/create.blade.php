@extends('layouts.app')

@section('title', 'Tambah Armada Kendaraan Baru - SPVMB')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Armada Kendaraan Baru</h1>
            <p class="page-subtitle">Daftarkan unit kendaraan dinas operasional dan buat kode QR inventaris otomatis.</p>
        </div>
        <div>
            <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="id_kendaraan">Kode Unit / Inventaris <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="id_kendaraan" name="id_kendaraan" class="form-control" value="{{ old('id_kendaraan', $suggestedId) }}" placeholder="Contoh: KND-006" required>
                    <span class="form-hint">Kode unik identitas unit kendaraan</span>
                    @error('id_kendaraan')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="plat_nomor">Nomor Polisi (Plat Nomor) <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="plat_nomor" name="plat_nomor" class="form-control" value="{{ old('plat_nomor') }}" placeholder="Contoh: B 1234 ABC" required>
                    <span class="form-hint">Nomor polisi resmi kendaraan dinas</span>
                    @error('plat_nomor')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="nama_kendaraan">Nama / Merk Kendaraan <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nama_kendaraan" name="nama_kendaraan" class="form-control" value="{{ old('nama_kendaraan') }}" placeholder="Contoh: Toyota Corolla Altis 1.8 V" required>
                @error('nama_kendaraan')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="kapasitas">Kapasitas Maksimal (Penumpang) <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="kapasitas" name="kapasitas" class="form-control" value="{{ old('kapasitas', 5) }}" min="1" max="60" required>
                    @error('kapasitas')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="status_ketersediaan">Status Ketersediaan Awal <span style="color: var(--danger);">*</span></label>
                    <select id="status_ketersediaan" name="status_ketersediaan" class="form-control" required>
                        <option value="Tersedia" {{ old('status_ketersediaan') === 'Tersedia' ? 'selected' : '' }}>Tersedia di Pool</option>
                        <option value="Sedang Dipinjam" {{ old('status_ketersediaan') === 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="foto_kendaraan">Foto Kendaraan (Opsional)</label>
                <input type="file" id="foto_kendaraan" name="foto_kendaraan" class="form-control" accept="image/*">
                <span class="form-hint">Maksimal 2MB (JPG, PNG). Jika dikosongkan, akan menggunakan ilustrasi standar.</span>
                @error('foto_kendaraan')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="background: var(--bg-elevated); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin: 1.5rem 0; font-size: 0.85rem; color: var(--text-muted);">
                ℹ️ Sistem akan <strong>secara otomatis membuat entitas QR Code</strong> pada tabel <code>qr_code</code> yang berelasi langsung dengan unit kendaraan ini.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    💾 Simpan Armada & Buat QR Code
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
