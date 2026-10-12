<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSirkulasi extends Model
{
    use HasFactory;

    protected $table = 'laporan_sirkulasi';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_laporan',
        'rentang_periode_mulai',
        'rentang_periode_akhir',
        'jenis_laporan',
        'total_transaksi',
        'id_staf_admin',
    ];

    protected $casts = [
        'rentang_periode_mulai' => 'date',
        'rentang_periode_akhir' => 'date',
    ];

    public function stafAdmin()
    {
        return $this->belongsTo(User::class, 'id_staf_admin', 'id_pengguna');
    }
}
