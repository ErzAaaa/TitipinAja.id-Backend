<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'id_member';

    protected $fillable = [
        'id_pengguna',
        'tanggal_daftar',
        'jenis_member',
        'diskon_decimal',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'diskon_decimal' => 'decimal:2',
    ];

    // Relasi Many-to-One dengan Pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}