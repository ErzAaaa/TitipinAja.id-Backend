<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'email' => 'required|string|email|max:255|unique:pengguna,email',
            'password' => 'required|string|min:6',
        ]);

        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Buat token langsung saat registrasi
        $token = $pengguna->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => $pengguna,
            'token' => $token // Kembalikan token agar user bisa langsung login
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // --- INI PERBAIKANNYA ---
        // Buat token baru untuk pengguna
        $token = $pengguna->createToken('authToken')->plainTextToken;
        // -------------------------

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $pengguna,
            'token' => $token // <-- Kembalikan token ke client
        ]);
    }
}
