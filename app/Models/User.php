<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'nama',
        'username',
        'password',
        'hak_akses',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke TransaksiPeminjaman (One-to-Many)
     */
    public function transaksi()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'id_pengguna', 'id_pengguna');
    }

    // Role helper methods
    public function isKaryawan(): bool
    {
        return $this->hak_akses === 'Karyawan';
    }

    public function isStafAdmin(): bool
    {
        return $this->hak_akses === 'Staf Admin';
    }

    public function isManajer(): bool
    {
        return $this->hak_akses === 'Manajer';
    }
}
