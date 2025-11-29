<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $table = 'tarif';
    protected $primaryKey = 'id_tarif';

    protected $fillable = [
        'jenis_tarif',
        'biaya',
        'keterangan_tarif',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    // Relasi One-to-Many dengan Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_tarif', 'id_tarif');
    }
}