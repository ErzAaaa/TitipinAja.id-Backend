<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetugasController extends Controller
{
    // GET - Tampilkan semua petugas
    public function index()
    {
        $petugas = Petugas::all();
        return response()->json([
            'success' => true,
            'data' => $petugas
        ], 200);
    }

    // POST - Tambah petugas baru
    public function store(Request $request)
    {
        // 1. Validasi Input (Fokus ke Email)
        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'required|string|max:100',
            'email'        => 'required|email|max:100|unique:petugas,email', // KUNCI LOGIN ADMIN
            'password'     => 'required|string|min:6',
            'no_telepon'   => 'required|string|max:20',
            'shift_kerja'  => 'required|string|max:20',
            'status'       => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Simpan ke Database
        $petugas = Petugas::create([
            'nama_petugas' => $request->nama_petugas,
            'email'        => $request->email,
            
            // TRICK DEADLINE: 
            // Jika database Anda masih maksa minta kolom 'username', 
            // kita isi otomatis pakai email biar tidak error.
            // Jika kolom username sudah dihapus di DB, baris ini boleh dihapus.
            'username'     => $request->email, 
            
            'password'     => Hash::make($request->password),
            'no_telepon'   => $request->no_telepon,
            'shift_kerja'  => $request->shift_kerja,
            'status'       => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil ditambahkan',
            'data' => $petugas
        ], 201);
    }

    // GET - Detail petugas
    public function show($id)
    {
        $petugas = Petugas::find($id);
        if (!$petugas) {
            return response()->json(['success' => false, 'message' => 'Petugas tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $petugas], 200);
    }

    // PUT - Update petugas
    public function update(Request $request, $id)
    {
        $petugas = Petugas::find($id);
        if (!$petugas) {
            return response()->json(['success' => false, 'message' => 'Petugas tidak ditemukan'], 404);
        }

        $pk = $petugas->getKeyName(); // Ambil nama primary key (misal: id_petugas)

        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'sometimes|string|max:100',
            'email'        => 'sometimes|email|max:100|unique:petugas,email,' . $id . ',' . $pk,
            'password'     => 'sometimes|string|min:6',
            'no_telepon'   => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->except('password');
        
        // Hash password baru jika ada
        if ($request->has('password') && $request->password != null) {
            $data['password'] = Hash::make($request->password);
        }

        // Update username juga kalau email berubah (Jaga-jaga DB masih butuh username)
        if ($request->has('email')) {
            $data['username'] = $request->email;
        }

        $petugas->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil diupdate',
            'data' => $petugas
        ], 200);
    }

    // DELETE - Hapus petugas
    public function destroy($id)
    {
        $petugas = Petugas::find($id);
        if (!$petugas) {
            return response()->json(['success' => false, 'message' => 'Petugas tidak ditemukan'], 404);
        }
        $petugas->delete();
        return response()->json(['success' => true, 'message' => 'Petugas berhasil dihapus'], 200);
    }
}