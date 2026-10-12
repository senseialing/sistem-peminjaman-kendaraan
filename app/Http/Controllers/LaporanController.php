<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiPeminjaman;
use App\Models\LaporanSirkulasi;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $mulai = $request->query('mulai', Carbon::now()->startOfMonth()->toDateString());
        $akhir = $request->query('akhir', Carbon::now()->toDateString());
        $jenis = $request->query('jenis', 'Bulanan');

        $transaksi = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereBetween('tanggal_pengajuan', [$mulai, $akhir])
            ->latest('tanggal_pengajuan')
            ->get();

        $auditLogs = LaporanSirkulasi::with('stafAdmin')
            ->latest()
            ->take(8)
            ->get();

        return view('laporan.index', compact('mulai', 'akhir', 'jenis', 'transaksi', 'auditLogs'));
    }

    public function cetak(Request $request)
    {
        $mulai = $request->query('mulai', Carbon::now()->startOfMonth()->toDateString());
        $akhir = $request->query('akhir', Carbon::now()->toDateString());
        $jenis = $request->query('jenis', 'Bulanan');

        $transaksi = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereBetween('tanggal_pengajuan', [$mulai, $akhir])
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        // Catat ke tabel LaporanSirkulasi sebagai log audit internal (halaman 2 spesifikasi)
        $prefix = 'LAP-' . Carbon::now()->format('Ym') . '-';
        $count = LaporanSirkulasi::where('id_laporan', 'like', "{$prefix}%")->count() + 1;
        $idLaporan = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
        while (LaporanSirkulasi::find($idLaporan)) {
            $count++;
            $idLaporan = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        LaporanSirkulasi::create([
            'id_laporan'            => $idLaporan,
            'rentang_periode_mulai' => $mulai,
            'rentang_periode_akhir' => $akhir,
            'jenis_laporan'         => $jenis,
            'total_transaksi'       => $transaksi->count(),
            'id_staf_admin'         => Auth::user()->id_pengguna,
        ]);

        return view('laporan.cetak', compact('mulai', 'akhir', 'jenis', 'transaksi', 'idLaporan'));
    }
}
