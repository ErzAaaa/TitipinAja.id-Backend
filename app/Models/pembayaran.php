<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_transaksi',
        'tgl_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'status',
    ];

    protected $casts = [
        'tgl_pembayaran' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    // Relasi Many-to-One dengan Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // Scope untuk pembayaran lunas
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    // Scope untuk pembayaran belum lunas
    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'belum_lunas');
    }
}