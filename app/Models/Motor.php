<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    protected $table = 'motor';
    protected $primaryKey = 'id_motor';

    protected $fillable = [
        'id_pengguna',
        'merk',
        'plat_nomor',
        'warna',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    // Relasi Many-to-One dengan Pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi One-to-Many dengan Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_motor', 'id_motor');
    }
}