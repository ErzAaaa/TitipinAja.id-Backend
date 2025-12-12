<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    // PERBAIKAN 1: Sesuaikan $fillable dengan nama kolom di database (id_pengguna)
    protected $fillable = [
        'id_pengguna',    // <-- Ubah dari 'user_id' menjadi 'id_pengguna'
        'id_motor',       // Pastikan ini id_motor (sesuai migrasi) bukan motor_id
        'id_petugas',     // Tambahkan ini agar id_petugas bisa disimpan
        'id_parkir_slot', // <-- Sesuaikan dengan controller Anda ($slot->id_slot)
        'kode_tiket',
        'jam_masuk',
        'jam_keluar',
        'total_biaya',
        'status',
        'metode_pembayaran',
        'kode_transaksi' // Jika memang ada kolom ini
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

    // PERBAIKAN 2: Arahkan ke model Pengguna::class, bukan User::class
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna'); 
    }

    public function parkirSlot()
    {
        // Sesuaikan parameter foreign key dengan kolom di tabel transaksi
        // Berdasarkan controller Anda: 'id_parkir_slot'
        return $this->belongsTo(ParkirSlot::class, 'id_parkir_slot', 'id_slot');
    }

    // ... scopes ...
}