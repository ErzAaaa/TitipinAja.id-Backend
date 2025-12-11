<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Login khusus Petugas / Admin
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Cari Petugas
        $petugas = Petugas::where('username', $request->username)->first();

        // Cek Password
        if (!$petugas || !Hash::check($request->password, $petugas->password)) {
            return response()->json(['message' => 'Username atau Password salah'], 401);
        }

        // Buat Token
        $token = $petugas->createToken('auth_token_petugas')->plainTextToken;

        return response()->json([
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $petugas
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