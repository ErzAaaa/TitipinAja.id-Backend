<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkirSlot extends Model
{
    use HasFactory;

    protected $table = 'parkir_slots';
    
    // PRIMARY KEY
    protected $primaryKey = 'id_slot'; 

    protected $fillable = [
        'nomor_slot', // PERBAIKAN: Gunakan nomor_slot (bukan kode_slot)
        'lokasi',
        'status',
    ];

    public function transaksis()
    {
        // Sesuaikan relasi: (Model, Foreign Key di Transaksi, Local Key di sini)
        // Asumsi di tabel transaksi nama kolomnya 'id_parkir_slot'
        return $this->hasMany(Transaksi::class, 'id_parkir_slot', 'id_slot');
    }

    public function scopeKosong($query)
    {
        return $query->where('status', 'Tersedia');
    }

    public function scopeTerisi($query)
    {
        return $query->where('status', 'Terisi');
    }
}