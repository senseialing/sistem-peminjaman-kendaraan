<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kendaraan;
use App\Models\TransaksiPeminjaman;
use App\Services\QrCodeService;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transaksi = TransaksiPeminjaman::with('kendaraan')
            ->where('id_pengguna', $user->id_pengguna)
            ->latest('tanggal_pengajuan')
            ->paginate(10);

        return view('peminjaman.index', compact('transaksi'));
    }

    public function create(Request $request)
    {
        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia')->get();
        $selectedKendaraanId = $request->query('id_kendaraan');

        return view('peminjaman.create', compact('kendaraanTersedia', 'selectedKendaraanId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kendaraan'       => 'required|exists:kendaraan,id_kendaraan',
            'tanggal_pengajuan'  => 'required|date|after_or_equal:today',
            'tanggal_selesai'    => 'required|date|after_or_equal:tanggal_pengajuan',
            'tujuan_operasional' => 'required|string|min:10|max:500',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai peminjaman.',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->id_kendaraan);
        if ($kendaraan->status_ketersediaan !== 'Tersedia') {
            return back()->with('error', 'Kendaraan yang dipilih sedang tidak tersedia.')->withInput();
        }

        // Generate ID Transaksi unik: TRX-YYYYMM-XXXX
        $prefix = 'TRX-' . Carbon::now()->format('Ym') . '-';
        $countThisMonth = TransaksiPeminjaman::where('id_transaksi', 'like', "{$prefix}%")->count() + 1;
        $idTransaksi = $prefix . str_pad($countThisMonth, 4, '0', STR_PAD_LEFT);
        while (TransaksiPeminjaman::find($idTransaksi)) {
            $countThisMonth++;
            $idTransaksi = $prefix . str_pad($countThisMonth, 4, '0', STR_PAD_LEFT);
        }

        $transaksi = TransaksiPeminjaman::create([
            'id_transaksi'           => $idTransaksi,
            'id_pengguna'            => Auth::user()->id_pengguna,
            'id_kendaraan'           => $request->id_kendaraan,
            'tanggal_pengajuan'      => $request->tanggal_pengajuan,
            'tanggal_selesai'        => $request->tanggal_selesai,
            'tujuan_operasional'     => $request->tujuan_operasional,
            'status_otorisasi'       => 'Pending',
            'status_sirkulasi'       => 'Tersedia',
            'timestamp_serah_terima' => null,
            'timestamp_kembali'      => null,
        ]);

        return redirect()->route('peminjaman.show', $transaksi->id_transaksi)
            ->with('success', "Pengajuan peminjaman berhasil dibuat dengan Nomor Tiket {$idTransaksi}. Menunggu persetujuan manajer.");
    }

    public function show($id)
    {
        $transaksi = TransaksiPeminjaman::with(['kendaraan.qrCode', 'pengguna'])->findOrFail($id);

        // Security check: Karyawan only view own ticket unless admin/manager
        $user = Auth::user();
        if ($user->isKaryawan() && $transaksi->id_pengguna !== $user->id_pengguna) {
            abort(403, 'Anda tidak memiliki hak akses melihat tiket ini.');
        }

        // Generate QR code SVG for this ticket
        $qrData = "TIKET:{$transaksi->id_transaksi}|UNIT:{$transaksi->id_kendaraan}|USER:{$transaksi->id_pengguna}";
        $qrSvg = QrCodeService::generateSvg($qrData, 180);

        return view('peminjaman.show', compact('transaksi', 'qrSvg'));
    }

    public function ajukanPengembalian($id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        if (Auth::user()->isKaryawan() && $transaksi->id_pengguna !== Auth::user()->id_pengguna) {
            abort(403);
        }

        $transaksi->update([
            'status_sirkulasi' => 'Menunggu ACC Pengembalian',
        ]);

        return back()->with('success', 'Permintaan pengembalian kendaraan telah dikirim. Silakan serahkan kunci fisik ke Staf Admin.');
    }
}
