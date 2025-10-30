<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_motor',
        'id_petugas',
        'id_tarif',
        'id_slot',
        'jam_masuk',
        'jam_keluar',
        'status',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];

    // Relasi Many-to-One dengan Motor
    public function motor()
    {
        return $this->belongsTo(Motor::class, 'id_motor', 'id_motor');
    }

    // Relasi Many-to-One dengan Petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    // Relasi Many-to-One dengan Tarif
    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    // Relasi Many-to-One dengan ParkirSlot
    public function parkirSlot()
    {
        return $this->belongsTo(ParkirSlot::class, 'id_slot', 'id_slot');
    }

    // Relasi One-to-One dengan Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }

    // Relasi One-to-One dengan KartuTitip
    public function kartuTitip()
    {
        return $this->hasOne(KartuTitip::class, 'id_transaksi', 'id_transaksi');
    }

    // Relasi One-to-Many dengan Riwayat
    public function riwayats()
    {
        return $this->hasMany(Riwayat::class, 'id_transaksi', 'id_transaksi');
    }

    // Scope untuk transaksi aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk transaksi selesai
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}