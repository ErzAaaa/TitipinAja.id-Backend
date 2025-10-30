<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';

    protected $fillable = [
        'nama_petugas',
        'username',
        'password',
        'no_telepon',
        'shift_kerja',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi One-to-Many dengan Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_petugas', 'id_petugas');
    }
}