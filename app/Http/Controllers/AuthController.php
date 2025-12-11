<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. UBAH VALIDASI: Dari 'username' menjadi 'email'
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email', // <-- Ganti jadi email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // 2. UBAH PENCARIAN: Cari petugas berdasarkan kolom 'email'
        $petugas = Petugas::where('email', $request->email)->first(); // <-- Ganti username jadi email

        // 3. Cek Password
        if (!$petugas || !Hash::check($request->password, $petugas->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        // 4. Buat Token
        $token = $petugas->createToken('auth_token_petugas')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Login Berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => $petugas
        ]);
    }

    // ... method logout dan me biarkan tetap sama ...
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Berhasil']);
    }
    
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}