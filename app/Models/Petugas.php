<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// 1. Ganti 'Model' biasa jadi 'Authenticatable'
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
// 2. Wajib import ini buat Token
use Laravel\Sanctum\HasApiTokens; 

class Petugas extends Authenticatable // <--- Ganti 'Model' jadi 'Authenticatable'
{
    // 3. Pasang Trait HasApiTokens di sini
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';

    protected $fillable = [
        'nama_petugas',
        'email',     // <--- WAJIB DITAMBAH (Sesuai kesepakatan Login via Email)
        'username',  // Boleh dibiarkan kalau di database kolom ini masih ada
        'password',
        'no_telepon',
        'shift_kerja',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token', // Tambahan standard Laravel
    ];

    // Relasi One-to-Many dengan Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_petugas', 'id_petugas');
    }
}