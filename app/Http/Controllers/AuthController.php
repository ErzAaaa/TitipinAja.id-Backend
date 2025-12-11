<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Login khusus Petugas / Admin (Via Email)
    public function login(Request $request)
    {
        // 1. Validasi Input (Ganti username -> email)
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email', // Wajib format email valid
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // 2. Cari Petugas Berdasarkan Email
        $petugas = Petugas::where('email', $request->email)->first();

        // 3. Cek Password
        if (!$petugas || !Hash::check($request->password, $petugas->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        // 4. Buat Token (Sanctum)
        $token = $petugas->createToken('auth_token_petugas')->plainTextToken;

        return response()->json([
            'success'      => true, // Tambahkan flag success agar mudah dicek di Flutter
            'message'      => 'Login Berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => $petugas
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token petugas yang sedang login
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Berhasil']);
    }
    
    // Untuk cek sesi di frontend admin
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}