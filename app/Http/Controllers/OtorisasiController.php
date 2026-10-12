<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPeminjaman;

class OtorisasiController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])->latest('tanggal_pengajuan');

        if ($status === 'pending') {
            $query->where('status_otorisasi', 'Pending');
        } elseif ($status === 'approved') {
            $query->where('status_otorisasi', 'Approved');
        } elseif ($status === 'rejected') {
            $query->where('status_otorisasi', 'Rejected');
        }

        $transaksi = $query->paginate(12);

        return view('otorisasi.index', compact('transaksi', 'status'));
    }

    public function approve(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        $request->validate([
            'catatan_manajer' => 'nullable|string|max:500',
        ]);

        $transaksi->update([
            'status_otorisasi' => 'Approved',
            'catatan_manajer'  => $request->catatan_manajer ?: 'Disetujui oleh Manajer Operasional.',
        ]);

        return back()->with('success', "Permohonan {$transaksi->id_transaksi} berhasil disetujui (Approved). Staf Admin kini dapat memproses serah terima kendaraan.");
    }

    public function reject(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        $request->validate([
            'catatan_manajer' => 'required|string|max:500',
        ]);

        $transaksi->update([
            'status_otorisasi' => 'Rejected',
            'catatan_manajer'  => $request->catatan_manajer,
        ]);

        return back()->with('info', "Permohonan {$transaksi->id_transaksi} telah ditolak (Rejected).");
    }
}
