<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use App\Models\Petugas;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * REGISTER
     * (Khusus untuk User/Member Publik)
     */
    public function register(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon'   => 'required|string|max:20',
            'alamat'       => 'required|string|max:500',
            'email'        => 'required|string|email|max:255|unique:pengguna,email',
            'password'     => 'required|string|min:6',
        ]);

        // 2. Simpan ke Database (Tabel Pengguna)
        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon'   => $request->no_telepon,
            'alamat'       => $request->alamat,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        // 3. Buat Token
        $token = $pengguna->createToken('authToken')->plainTextToken;

        // 4. Kirim Response
        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data'    => $pengguna,
            'token'   => $token,
            'role'    => 'user' // Default role
        ], 201);
    }

    /**
     * LOGIN
     * (Satu Pintu untuk Admin & User)
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:admin,user', // KUNCI: Petunjuk arah dari Flutter
        ]);

        $user = null;

        // 2. LOGIKA SWITCH (Tanpa kolom role di DB)
        if ($request->role === 'admin') {
            // Jika login sebagai Admin, cari di tabel PETUGAS
            $user = Petugas::where('email', $request->email)->first();
        } else {
            // Jika login sebagai User, cari di tabel PENGGUNA
            $user = Pengguna::where('email', $request->email)->first();
        }

        // 3. Cek Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login Gagal! Email tidak ditemukan atau password salah.'
            ], 401);
        }

        // 4. Buat Token
        $token = $user->createToken('authToken')->plainTextToken;

        // 5. Kirim Response
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil sebagai ' . $request->role,
            'data'    => $user,
            'token'   => $token,
            'role'    => $request->role // Kembalikan info role ke Flutter agar tahu harus ke halaman mana
        ], 200);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Logout'
        ], 200);
    }
}