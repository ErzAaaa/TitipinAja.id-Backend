<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    // Kolom yang boleh diisi oleh Petugas
    protected $fillable = [
        'nama',
        'alamat',
        'no_telepon',
    ];

    // Tidak ada lagi hidden password karena user tidak login
}