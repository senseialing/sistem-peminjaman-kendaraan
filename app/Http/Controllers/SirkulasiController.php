<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPeminjaman;
use App\Models\Kendaraan;
use Carbon\Carbon;

class SirkulasiController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'serah-terima');

        // Antrean serah terima kunci: sudah di-approve manajer, belum diserahterimakan
        $antreanSerahTerima = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->where('status_otorisasi', 'Approved')
            ->whereNull('timestamp_serah_terima')
            ->latest('tanggal_pengajuan')
            ->get();

        // Antrean pengembalian: sudah serah terima, belum kembali
        $antreanPengembalian = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereNotNull('timestamp_serah_terima')
            ->whereNull('timestamp_kembali')
            ->latest('timestamp_serah_terima')
            ->get();

        // Riwayat sirkulasi selesai
        $riwayatSirkulasi = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereNotNull('timestamp_kembali')
            ->latest('timestamp_kembali')
            ->take(10)
            ->get();

        return view('sirkulasi.index', compact('tab', 'antreanSerahTerima', 'antreanPengembalian', 'riwayatSirkulasi'));
    }

    public function prosesSerahTerima(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::with('kendaraan')->findOrFail($id);

        if ($transaksi->status_otorisasi !== 'Approved') {
            return back()->with('error', 'Transaksi ini belum diotorisasi oleh Manajer.');
        }

        if ($transaksi->timestamp_serah_terima) {
            return back()->with('error', 'Kunci kendaraan untuk transaksi ini sudah diserahterimakan sebelumnya.');
        }

        $now = Carbon::now();

        // Update transaksi
        $transaksi->update([
            'timestamp_serah_terima' => $now,
            'status_sirkulasi'       => 'Menunggu ACC Pengembalian',
        ]);

        // Update status armada menjadi Sedang Dipinjam
        $transaksi->kendaraan->update([
            'status_ketersediaan' => 'Sedang Dipinjam',
        ]);

        return back()->with('success', "Kunci kendaraan [{$transaksi->kendaraan->plat_nomor}] berhasil diserahterimakan kepada {$transaksi->pengguna->nama} pada {$now->translatedFormat('d M Y H:i')} WIB.");
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::with('kendaraan')->findOrFail($id);

        if (!$transaksi->timestamp_serah_terima) {
            return back()->with('error', 'Kendaraan belum diserahterimakan.');
        }

        if ($transaksi->timestamp_kembali) {
            return back()->with('error', 'Kendaraan ini sudah diproses pengembaliannya.');
        }

        $now = Carbon::now();

        // Update transaksi
        $transaksi->update([
            'timestamp_kembali' => $now,
            'status_sirkulasi'  => 'Tersedia',
        ]);

        // Update status armada kembali Tersedia
        $transaksi->kendaraan->update([
            'status_ketersediaan' => 'Tersedia',
        ]);

        return back()->with('success', "Pengembalian kendaraan [{$transaksi->kendaraan->plat_nomor}] telah diverifikasi pada {$now->translatedFormat('d M Y H:i')} WIB. Status armada kini Tersedia.");
    }

    public function scan(Request $request)
    {
        $keyword = $request->query('keyword');
        $hasil = null;

        if ($keyword) {
            // Bersihkan keyword jika berbentuk string QR data: e.g. TIKET:TRX-202610-0001 atau UNIT:KND-001
            $cleanCode = $keyword;
            if (preg_match('/(?:TIKET|UNIT):([A-Za-z0-9\-_]+)/', $keyword, $matches)) {
                $cleanCode = $matches[1];
            }

            $hasil = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
                ->where('id_transaksi', $cleanCode)
                ->orWhere('id_kendaraan', $cleanCode)
                ->orWhereHas('kendaraan', function ($q) use ($cleanCode) {
                    $q->where('plat_nomor', 'like', "%{$cleanCode}%");
                })
                ->latest()
                ->first();
        }

        return view('sirkulasi.scan', compact('keyword', 'hasil'));
    }
}
