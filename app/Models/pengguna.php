<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama_lengkap',
        'no_telepon',
        'alamat',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi One-to-One dengan Member
    public function member()
    {
        return $this->hasOne(Member::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi One-to-Many dengan Motor
    public function motors()
    {
        return $this->hasMany(Motor::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi One-to-Many dengan Riwayat
    public function riwayats()
    {
        return $this->hasMany(Riwayat::class, 'id_pengguna', 'id_pengguna');
    }
}