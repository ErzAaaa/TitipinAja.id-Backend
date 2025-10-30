<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::all();
        return response()->json([
            'success' => true,
            'data' => $petugas
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:petugas,username',
            'password' => 'required|string|min:6',
            'no_telepon' => 'required|string|max:20',
            'shift_kerja' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $petugas = Petugas::create([
            'nama_petugas' => $request->nama_petugas,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'no_telepon' => $request->no_telepon,
            'shift_kerja' => $request->shift_kerja,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil ditambahkan',
            'data' => $petugas
        ], 201);
    }

    public function show($id)
    {
        $petugas = Petugas::find($id);

        if (!$petugas) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $petugas
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::find($id);

        if (!$petugas) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'sometimes|string|max:100',
            'username' => 'sometimes|string|max:50|unique:petugas,username,' . $id . ',id_petugas',
            'password' => 'sometimes|string|min:6',
            'no_telepon' => 'sometimes|string|max:20',
            'shift_kerja' => 'sometimes|string|max:20',
            'status' => 'sometimes|string|max:20',
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

        $petugas->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil diupdate',
            'data' => $petugas
        ], 200);
    }

    public function destroy($id)
    {
        $petugas = Petugas::find($id);

        if (!$petugas) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan'
            ], 404);
        }

        $petugas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil dihapus'
        ], 200);
    }
}