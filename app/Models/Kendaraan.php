<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kendaraan',
        'nama_kendaraan',
        'plat_nomor',
        'foto_kendaraan',
        'kapasitas',
        'status_ketersediaan',
    ];

    /**
     * Relasi ke QR Code (One-to-One)
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'id_kendaraan', 'id_kendaraan');
    }

    /**
     * Relasi ke Transaksi Peminjaman (One-to-Many)
     */
    public function transaksi()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_kendaraan && file_exists(public_path($this->foto_kendaraan))) {
            return asset($this->foto_kendaraan);
        }
        return asset('images/vehicles/default.svg');
    }
}
