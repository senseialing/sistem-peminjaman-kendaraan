@extends('layouts.app')

@section('title', 'Form Pengajuan Peminjaman Kendaraan Dinas - SPVMB')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Pengajuan Peminjaman Kendaraan Dinas</h1>
            <p class="page-subtitle">Isi formulir berikut untuk mengajukan permohonan kendaraan dinas operasional.</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Dashboard</a>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf

            <!-- Peminjam Info -->
            <div style="background: var(--bg-elevated); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem; border: 1px solid var(--border-color);">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Pemohon Dinas</div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <strong style="font-size: 1.05rem;">{{ Auth::user()->nama }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.88rem; margin-left: 0.5rem;">(NIK / Username: {{ Auth::user()->username }})</span>
                    </div>
                    <span class="role-badge karyawan">ID: {{ Auth::user()->id_pengguna }}</span>
                </div>
            </div>

            <!-- Pilih Kendaraan -->
            <div class="form-group">
                <label class="form-label" for="id_kendaraan">Pilih Armada Kendaraan Operasional <span style="color: var(--danger);">*</span></label>
                <select name="id_kendaraan" id="id_kendaraan" class="form-control" required>
                    <option value="">-- Pilih Kendaraan Tersedia --</option>
                    @foreach($kendaraanTersedia as $k)
                        <option value="{{ $k->id_kendaraan }}" {{ (old('id_kendaraan', $selectedKendaraanId) == $k->id_kendaraan) ? 'selected' : '' }}>
                            {{ $k->nama_kendaraan }} — [ {{ $k->plat_nomor }} ] (Kapasitas: {{ $k->kapasitas }} Orang)
                        </option>
                    @endforeach
                </select>
                @error('id_kendaraan')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Rentang Tanggal Peminjaman -->
            <div class="grid grid-cols-2" style="gap: 1.25rem; margin-bottom: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="tanggal_pengajuan">
                        📅 Tanggal Mulai Penggunaan <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" name="tanggal_pengajuan" id="tanggal_pengajuan" class="form-control" 
                        value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" 
                        min="{{ date('Y-m-d') }}" required onchange="updateMinEndDate()">
                    <span class="form-hint">Tanggal awal keberangkatan dinas</span>
                    @error('tanggal_pengajuan')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="tanggal_selesai">
                        🏁 Tanggal Selesai / Pengembalian <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                        value="{{ old('tanggal_selesai', date('Y-m-d')) }}" 
                        min="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required onchange="calculateDuration()">
                    <span class="form-hint" id="durasi-hint">Pilih tanggal akhir peminjaman</span>
                    @error('tanggal_selesai')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Durasi Badge -->
            <div id="durasi-badge-box" style="margin-bottom: 1.5rem; background: rgba(225, 29, 45, 0.08); border: 1px solid rgba(225, 29, 45, 0.25); border-radius: var(--radius-sm); padding: 0.65rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    ⏱️ Estimasi Durasi Peminjaman Aset:
                </div>
                <div id="durasi-text" style="font-weight: 700; color: var(--primary); font-size: 0.95rem;">
                    1 Hari
                </div>
            </div>

            <!-- Keperluan Operasional -->
            <div class="form-group">
                <label class="form-label" for="tujuan_operasional">Tujuan & Keperluan Operasional <span style="color: var(--danger);">*</span></label>
                <textarea name="tujuan_operasional" id="tujuan_operasional" class="form-control" rows="4" placeholder="Jelaskan secara spesifik agenda dinas, lokasi tujuan, dan pihak terkait..." required>{{ old('tujuan_operasional') }}</textarea>
                <span class="form-hint">Contoh: Menghadiri rapat koordinasi teknis bersama Badan Pengawas di kantor cabang Bekasi & penyerahan berkas audit.</span>
                @error('tujuan_operasional')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- SOP & Ketentuan Notifikasi -->
            <div style="background: var(--bg-elevated); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin: 1.5rem 0; font-size: 0.85rem; color: var(--text-muted);">
                <strong style="color: var(--text-main);">📌 Prosedur Operasional (SOP):</strong>
                <ol style="margin-top: 0.4rem; padding-left: 1.2rem; line-height: 1.6;">
                    <li>Permohonan akan diteruskan ke <strong>Manajer Operasional</strong> untuk otorisasi persetujuan.</li>
                    <li>Setelah berstatus <strong>Approved</strong>, tunjukkan tiket digital ke <strong>Staf Admin</strong> untuk serah terima kunci.</li>
                    <li>Setelah dinas selesai, kembalikan unit ke pool armada untuk diverifikasi oleh staf admin.</li>
                </ol>
            </div>

            <!-- Checkbox Persetujuan Sesuai Referensi -->
            <div style="margin-bottom: 1.5rem; padding: 0.75rem 0;">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                    <input type="checkbox" required style="margin-top: 0.2rem; accent-color: var(--primary); width: 18px; height: 18px;">
                    <span>Saya bertanggung jawab penuh atas aset yang dipinjam dan menyetujui seluruh syarat & ketentuan <strong>SPV Asset Management</strong>.</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-weight: 700; letter-spacing: 0.03em;">
                    ➤ AJUKAN PEMINJAMAN
                </button>
        </form>
    </div>
</div>

<script>
function updateMinEndDate() {
    const startDateInput = document.getElementById('tanggal_pengajuan');
    const endDateInput = document.getElementById('tanggal_selesai');
    
    if (startDateInput.value) {
        endDateInput.min = startDateInput.value;
        if (endDateInput.value < startDateInput.value) {
            endDateInput.value = startDateInput.value;
        }
    }
    calculateDuration();
}

function calculateDuration() {
    const startDateInput = document.getElementById('tanggal_pengajuan');
    const endDateInput = document.getElementById('tanggal_selesai');
    const durasiText = document.getElementById('durasi-text');
    const durasiHint = document.getElementById('durasi-hint');
    
    if (startDateInput.value && endDateInput.value) {
        const start = new Date(startDateInput.value);
        const end = new Date(endDateInput.value);
        
        const diffTime = end - start;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive
        
        if (diffDays > 0) {
            durasiText.innerText = diffDays + ' Hari (' + diffDays + ' x 24 Jam)';
            durasiHint.innerText = 'Total durasi peminjaman: ' + diffDays + ' Hari';
        } else {
            durasiText.innerText = '1 Hari';
            durasiHint.innerText = 'Pilih tanggal akhir peminjaman';
        }
    }
}

// Initial calculation
document.addEventListener('DOMContentLoaded', calculateDuration);
</script>
@endsection
