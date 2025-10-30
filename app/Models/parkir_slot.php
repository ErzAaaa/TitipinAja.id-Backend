<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkirSlot extends Model
{
    use HasFactory;

    protected $table = 'parkir_slot';
    protected $primaryKey = 'id_slot';

    protected $fillable = [
        'kode_slot',
        'lokasi',
        'status',
    ];

    // Relasi One-to-Many dengan Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_slot', 'id_slot');
    }

    // Scope untuk slot kosong
    public function scopeKosong($query)
    {
        return $query->where('status', 'kosong');
    }

    // Scope untuk slot terisi
    public function scopeTerisi($query)
    {
        return $query->where('status', 'terisi');
    }
}