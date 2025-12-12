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
        'id_pengguna',    // SUDAH BENAR
        'id_motor',       // SUDAH BENAR
        'id_petugas',
        'id_slot',        // <--- PERBAIKAN: Gunakan 'id_slot' sesuai database (bukan id_parkir_slot)
        'kode_tiket',
        'jam_masuk',
        'jam_keluar',
        'total_biaya',
        'status',
        'metode_pembayaran',
        'kode_transaksi'
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    // ==========================
    // RELATIONS
    // ==========================

    public function motor()
    {
        return $this->belongsTo(Motor::class, 'id_motor', 'id_motor');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function parkirSlot()
    {
        // PERBAIKAN: 
        // 1. Nama kolom di tabel transaksi adalah 'id_slot'
        // 2. Nama kolom di tabel parkir_slots adalah 'id_slot'
        return $this->belongsTo(ParkirSlot::class, 'id_slot', 'id_slot');
    }
}