<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_code';
    protected $primaryKey = 'id_qrcode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_qrcode',
        'id_kendaraan',
        'data_matriks',
    ];

    /**
     * Relasi balik ke Kendaraan (Belongs-to)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
