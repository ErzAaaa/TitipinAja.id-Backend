<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuTitip extends Model
{
    use HasFactory;

    protected $table = 'kartu_titip';
    protected $primaryKey = 'id_kartu';

    protected $fillable = [
        'id_transaksi',
        'nomor_kartu',
        'status',
    ];

    // Relasi Many-to-One dengan Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // Scope untuk kartu yang digunakan
    public function scopeDigunakan($query)
    {
        return $query->where('status', 'digunakan');
    }

    // Scope untuk kartu yang tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    // Scope untuk kartu hilang
    public function scopeHilang($query)
    {
        return $query->where('status', 'hilang');
    }
}