<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'transaksi_peminjaman';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'id_pengguna',
        'id_kendaraan',
        'tanggal_pengajuan',
        'tanggal_selesai',
        'tujuan_operasional',
        'status_otorisasi',
        'status_sirkulasi',
        'timestamp_serah_terima',
        'timestamp_kembali',
        'catatan_manajer',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_selesai' => 'date',
        'timestamp_serah_terima' => 'datetime',
        'timestamp_kembali' => 'datetime',
    ];

    /**
     * Relasi ke Pengguna/User (Belongs-to)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke Kendaraan (Belongs-to)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
