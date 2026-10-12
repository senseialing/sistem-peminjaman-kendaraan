<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Support\Str;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Kendaraan::with('qrCode');

        if ($status) {
            $query->where('status_ketersediaan', $status);
        }

        $kendaraans = $query->latest()->paginate(10);

        return view('kendaraan.index', compact('kendaraans', 'status'));
    }

    public function create()
    {
        // Suggest unit ID
        $count = Kendaraan::count() + 1;
        $suggestedId = 'KND-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        while (Kendaraan::find($suggestedId)) {
            $count++;
            $suggestedId = 'KND-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        return view('kendaraan.create', compact('suggestedId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kendaraan'        => 'required|string|unique:kendaraan,id_kendaraan|max:20',
            'nama_kendaraan'      => 'required|string|max:100',
            'plat_nomor'          => 'required|string|max:20',
            'kapasitas'           => 'required|integer|min:1|max:60',
            'status_ketersediaan' => 'required|in:Tersedia,Sedang Dipinjam',
            'foto_kendaraan'      => 'nullable|image|max:2048',
        ]);

        $fotoPath = 'images/vehicles/default.jpg';
        if ($request->hasFile('foto_kendaraan')) {
            $file = $request->file('foto_kendaraan');
            $filename = time() . '_' . Str::slug($request->nama_kendaraan) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/vehicles'), $filename);
            $fotoPath = 'uploads/vehicles/' . $filename;
        }

        $kendaraan = Kendaraan::create([
            'id_kendaraan'        => strtoupper($request->id_kendaraan),
            'nama_kendaraan'      => $request->nama_kendaraan,
            'plat_nomor'          => strtoupper($request->plat_nomor),
            'foto_kendaraan'      => $fotoPath,
            'kapasitas'           => $request->kapasitas,
            'status_ketersediaan' => $request->status_ketersediaan,
        ]);

        // Auto generate QR Code untuk kendaraan
        $qrId = 'QR-' . substr($kendaraan->id_kendaraan, 4);
        if (QrCode::find($qrId)) {
            $qrId = 'QR-' . strtoupper(Str::random(5));
        }

        QrCode::create([
            'id_qrcode'    => $qrId,
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'data_matriks' => "UNIT:{$kendaraan->id_kendaraan}|PLAT:{$kendaraan->plat_nomor}|ID:{$qrId}",
        ]);

        return redirect()->route('kendaraan.show', $kendaraan->id_kendaraan)
            ->with('success', "Kendaraan {$kendaraan->nama_kendaraan} ({$kendaraan->plat_nomor}) dan QR Code inventaris berhasil ditambahkan!");
    }

    public function show($id)
    {
        $kendaraan = Kendaraan::with(['qrCode', 'transaksi.pengguna'])->findOrFail($id);

        $qrData = $kendaraan->qrCode ? $kendaraan->qrCode->data_matriks : "UNIT:{$kendaraan->id_kendaraan}|PLAT:{$kendaraan->plat_nomor}";
        $qrSvg = QrCodeService::generateSvg($qrData, 200);

        return view('kendaraan.show', compact('kendaraan', 'qrSvg'));
    }

    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        return view('kendaraan.edit', compact('kendaraan'));
    }

    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'nama_kendaraan'      => 'required|string|max:100',
            'plat_nomor'          => 'required|string|max:20',
            'kapasitas'           => 'required|integer|min:1|max:60',
            'status_ketersediaan' => 'required|in:Tersedia,Sedang Dipinjam',
            'foto_kendaraan'      => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama_kendaraan'      => $request->nama_kendaraan,
            'plat_nomor'          => strtoupper($request->plat_nomor),
            'kapasitas'           => $request->kapasitas,
            'status_ketersediaan' => $request->status_ketersediaan,
        ];

        if ($request->hasFile('foto_kendaraan')) {
            $file = $request->file('foto_kendaraan');
            $filename = time() . '_' . Str::slug($request->nama_kendaraan) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/vehicles'), $filename);
            $data['foto_kendaraan'] = 'uploads/vehicles/' . $filename;
        }

        $kendaraan->update($data);

        return redirect()->route('kendaraan.index')
            ->with('success', "Data armada {$kendaraan->nama_kendaraan} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $nama = $kendaraan->nama_kendaraan;
        $kendaraan->delete();

        return redirect()->route('kendaraan.index')
            ->with('success', "Kendaraan {$nama} berhasil dihapus dari sistem.");
    }
}
