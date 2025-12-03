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
        'id_pengguna', 
        'id_motor', 
        'id_petugas', 
        'id_parkir_slot', // SUDAH BENAR (Sesuai Migration)
        'jam_masuk', 
        'jam_keluar', 
        'total_biaya', 
        'status'
        // 'id_tarif' DIHAPUS karena kita hitung manual di controller
    ];

    protected $casts = [
        'jam_masuk' => 'datetime', // Biarkan datetime agar mudah dihitung Carbon
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

    // Tambahan: Relasi ke User (Pengguna)
    public function pengguna()
    {
        // Sesuaikan 'User::class' dengan nama model user Anda (misal: Pengguna::class)
        return $this->belongsTo(User::class, 'id_pengguna', 'id'); 
    }

    // PERBAIKAN PENTING: Nama kolom foreign key & owner key
    public function parkirSlot()
    {
        // Parameter 2: Foreign Key di tabel transaksi (id_parkir_slot)
        // Parameter 3: Owner Key di tabel parkir_slots (id_parkir_slot)
        return $this->belongsTo(ParkirSlot::class, 'id_parkir_slot', 'id_parkir_slot');
    }

    // ==========================
    // SCOPES (Untuk Filter Mudah)
    // ==========================

    // Scope untuk transaksi aktif (status = 'Masuk')
    public function scopeAktif($query)
    {
        return $query->where('status', 'Masuk');
    }

    // Scope untuk transaksi selesai (status = 'Selesai')
    public function scopeSelesai($query)
    {
        return $query->where('status', 'Selesai');
    }
}