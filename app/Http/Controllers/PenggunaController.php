<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PenggunaController extends Controller
{
    // GET - Tampilkan semua pengguna (Fungsi READ)
    public function index()
    {
        $pengguna = Pengguna::all();
        return response()->json([
            'success' => true,
            'data' => $pengguna
        ], 200);
    }

    // POST - Tambah pengguna baru (Fungsi CREATE)
    // Catatan: Ini tumpang tindih dengan register() di AuthController Anda.
    // Anda mungkin hanya perlu salah satu.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:100',
            'no_telepon' => 'required|int|max:14',
            'alamat' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:pengguna,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna
        ], 201);
    }

    // GET - Tampilkan detail pengguna (Fungsi READ by ID)
    public function show($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pengguna
        ], 200);
    }

    // PUT/PATCH - Update pengguna (Fungsi UPDATE)
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'sometimes|string|max:100',
            'no_telepon' => 'sometimes|int|max:14',
            'alamat' => 'sometimes|string|max:100',
            // Gunakan primary key kustom Anda 'id_pengguna'
            'email' => 'sometimes|email|max:100|unique:pengguna,email,' . $id . ',id_pengguna',
            'password' => 'sometimes|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('password');
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diupdate',
            'data' => $pengguna
        ], 200);
    }

    // DELETE - Hapus pengguna (Fungsi DELETE)
    public function destroy($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $pengguna->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ], 200);
    }
}
