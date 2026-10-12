<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kendaraan;
use App\Models\TransaksiPeminjaman;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isKaryawan()) {
            return $this->karyawanDashboard($user);
        } elseif ($user->isManajer()) {
            return $this->manajerDashboard($user);
        } elseif ($user->isStafAdmin()) {
            return $this->adminDashboard($user);
        }

        abort(403);
    }

    private function karyawanDashboard($user)
    {
        $peminjamanAktif = TransaksiPeminjaman::with('kendaraan')
            ->where('id_pengguna', $user->id_pengguna)
            ->where('status_otorisasi', 'Approved')
            ->whereNull('timestamp_kembali')
            ->latest()
            ->first();

        $countPending = TransaksiPeminjaman::where('id_pengguna', $user->id_pengguna)
            ->where('status_otorisasi', 'Pending')
            ->count();

        $countApproved = TransaksiPeminjaman::where('id_pengguna', $user->id_pengguna)
            ->where('status_otorisasi', 'Approved')
            ->count();

        $riwayatTransaksi = TransaksiPeminjaman::with('kendaraan')
            ->where('id_pengguna', $user->id_pengguna)
            ->latest('tanggal_pengajuan')
            ->take(10)
            ->get();

        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia')->get();

        return view('dashboard.karyawan', compact(
            'peminjamanAktif',
            'countPending',
            'countApproved',
            'riwayatTransaksi',
            'kendaraanTersedia'
        ));
    }

    private function manajerDashboard($user)
    {
        $countPending = TransaksiPeminjaman::where('status_otorisasi', 'Pending')->count();
        $countApproved = TransaksiPeminjaman::where('status_otorisasi', 'Approved')->count();
        $countRejected = TransaksiPeminjaman::where('status_otorisasi', 'Rejected')->count();
        $totalTransaksi = TransaksiPeminjaman::count();

        $antreanOtorisasi = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->where('status_otorisasi', 'Pending')
            ->latest('tanggal_pengajuan')
            ->get();

        $riwayatOtorisasi = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereIn('status_otorisasi', ['Approved', 'Rejected'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('dashboard.manajer', compact(
            'countPending',
            'countApproved',
            'countRejected',
            'totalTransaksi',
            'antreanOtorisasi',
            'riwayatOtorisasi'
        ));
    }

    private function adminDashboard($user)
    {
        $totalKendaraan = Kendaraan::count();
        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia')->count();
        $kendaraanDipinjam = Kendaraan::where('status_ketersediaan', 'Sedang Dipinjam')->count();

        // Siap serah terima: Manajer Approved tapi belum serah terima kunci
        $siapSerahTerima = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->where('status_otorisasi', 'Approved')
            ->whereNull('timestamp_serah_terima')
            ->get();

        // Sedang berjalan / menunggu pengembalian: Sudah serah terima tapi belum kembali
        $sedangDipinjam = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->whereNotNull('timestamp_serah_terima')
            ->whereNull('timestamp_kembali')
            ->get();

        $transaksiTerbaru = TransaksiPeminjaman::with(['pengguna', 'kendaraan'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.admin', compact(
            'totalKendaraan',
            'kendaraanTersedia',
            'kendaraanDipinjam',
            'siapSerahTerima',
            'sedangDipinjam',
            'transaksiTerbaru'
        ));
    }
}
