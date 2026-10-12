<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\QrCode;
use App\Models\TransaksiPeminjaman;
use App\Models\LaporanSirkulasi;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Users (Karyawan, Staf Admin, Manajer)
        $admin = User::create([
            'id_pengguna' => 'ADM-001',
            'nama'        => 'Budi Santoso',
            'username'    => 'admin',
            'password'    => Hash::make('password123'),
            'hak_akses'   => 'Staf Admin',
        ]);

        $manajer = User::create([
            'id_pengguna' => 'MGR-001',
            'nama'        => 'Hendra Wijaya, S.T.',
            'username'    => 'manajer',
            'password'    => Hash::make('password123'),
            'hak_akses'   => 'Manajer',
        ]);

        $karyawan1 = User::create([
            'id_pengguna' => 'EMP-001',
            'nama'        => 'Siti Rahmawati',
            'username'    => 'karyawan',
            'password'    => Hash::make('password123'),
            'hak_akses'   => 'Karyawan',
        ]);

        $karyawan2 = User::create([
            'id_pengguna' => 'EMP-002',
            'nama'        => 'Ahmad Pratama Putra',
            'username'    => 'ahmad',
            'password'    => Hash::make('password123'),
            'hak_akses'   => 'Karyawan',
        ]);

        // 2. Seed Kendaraan
        $kendaraans = [
            [
                'id_kendaraan'        => 'KND-001',
                'nama_kendaraan'     => 'Toyota Avanza 1.5 G TSS',
                'plat_nomor'          => 'B 1928 KDA',
                'foto_kendaraan'      => 'images/vehicles/avanza.jpg',
                'kapasitas'           => 7,
                'status_ketersediaan' => 'Sedang Dipinjam',
            ],
            [
                'id_kendaraan'        => 'KND-002',
                'nama_kendaraan'     => 'Toyota Innova Reborn 2.4 V AT',
                'plat_nomor'          => 'B 2341 TQZ',
                'foto_kendaraan'      => 'images/vehicles/innova.jpg',
                'kapasitas'           => 8,
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kendaraan'        => 'KND-003',
                'nama_kendaraan'     => 'Mitsubishi Pajero Sport Dakar 4x2',
                'plat_nomor'          => 'B 7777 WBO',
                'foto_kendaraan'      => 'images/vehicles/pajero.jpg',
                'kapasitas'           => 7,
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kendaraan'        => 'KND-004',
                'nama_kendaraan'     => 'Honda HR-V 1.5 SE Prestige',
                'plat_nomor'          => 'B 4562 UPL',
                'foto_kendaraan'      => 'images/vehicles/hrv.jpg',
                'kapasitas'           => 5,
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kendaraan'        => 'KND-005',
                'nama_kendaraan'     => 'Daihatsu Gran Max Minibus Operasional',
                'plat_nomor'          => 'B 9012 DKL',
                'foto_kendaraan'      => 'images/vehicles/granmax.jpg',
                'kapasitas'           => 8,
                'status_ketersediaan' => 'Tersedia',
            ],
        ];

        foreach ($kendaraans as $k) {
            Kendaraan::create($k);

            // 3. Seed QR Code untuk setiap kendaraan
            QrCode::create([
                'id_qrcode'    => 'QR-' . substr($k['id_kendaraan'], 4),
                'id_kendaraan' => $k['id_kendaraan'],
                'data_matriks' => 'UNIT:' . $k['id_kendaraan'] . '|PLAT:' . $k['plat_nomor'] . '|TOKEN:' . md5($k['id_kendaraan']),
            ]);
        }

        // 4. Seed Transaksi Peminjaman
        // Transaksi 1: Sedang Dipinjam (Menunggu ACC Pengembalian)
        TransaksiPeminjaman::create([
            'id_transaksi'           => 'TRX-202610-0001',
            'id_pengguna'            => 'EMP-001',
            'id_kendaraan'           => 'KND-001',
            'tanggal_pengajuan'      => '2026-10-06',
            'tujuan_operasional'     => 'Kunjungan inspeksi ke kantor cabang Serang & peninjauan fasilitas lapangan.',
            'status_otorisasi'       => 'Approved',
            'status_sirkulasi'       => 'Menunggu ACC Pengembalian',
            'timestamp_serah_terima' => '2026-10-06 08:30:00',
            'timestamp_kembali'      => null,
            'catatan_manajer'        => 'Disetujui. Harap gunakan bahan bakar standar Pertamax & cek tekanan ban.',
        ]);

        // Transaksi 2: Menunggu Otorisasi Manajer (Pending)
        TransaksiPeminjaman::create([
            'id_transaksi'           => 'TRX-202610-0002',
            'id_pengguna'            => 'EMP-002',
            'id_kendaraan'           => 'KND-002',
            'tanggal_pengajuan'      => '2026-10-07',
            'tujuan_operasional'     => 'Antar jemput perwakilan Kementerian ESDM di Bandara Internasional Soekarno-Hatta.',
            'status_otorisasi'       => 'Pending',
            'status_sirkulasi'       => 'Tersedia',
            'timestamp_serah_terima' => null,
            'timestamp_kembali'      => null,
            'catatan_manajer'        => null,
        ]);

        // Transaksi 3: Riwayat Selesai
        TransaksiPeminjaman::create([
            'id_transaksi'           => 'TRX-202610-0003',
            'id_pengguna'            => 'EMP-001',
            'id_kendaraan'           => 'KND-004',
            'tanggal_pengajuan'      => '2026-10-01',
            'tujuan_operasional'     => 'Meeting koordinasi mitra logistik di kawasan industri GIIC Cikarang.',
            'status_otorisasi'       => 'Approved',
            'status_sirkulasi'       => 'Tersedia',
            'timestamp_serah_terima' => '2026-10-01 09:00:00',
            'timestamp_kembali'      => '2026-10-01 17:45:00',
            'catatan_manajer'        => 'Disetujui sesuai agenda dinas resmi.',
        ]);

        // 5. Seed Laporan Sirkulasi
        LaporanSirkulasi::create([
            'id_laporan'            => 'LAP-202609-001',
            'rentang_periode_mulai' => '2026-09-01',
            'rentang_periode_akhir' => '2026-09-30',
            'jenis_laporan'         => 'Bulanan',
            'total_transaksi'       => 14,
            'id_staf_admin'         => 'ADM-001',
        ]);
    }
}
