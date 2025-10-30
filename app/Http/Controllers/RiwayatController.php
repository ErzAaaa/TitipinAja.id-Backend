<?php

namespace App\Http\Controllers;

use App\Models\Riwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Riwayat::with(['transaksi.motor', 'pengguna'])->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $riwayat
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'keterangan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $riwayat = Riwayat::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil ditambahkan',
            'data' => $riwayat->load(['transaksi.motor', 'pengguna'])
        ], 201);
    }

    public function show($id)
    {
        $riwayat = Riwayat::with(['transaksi.motor', 'pengguna'])->find($id);

        if (!$riwayat) {
            return response()->json([
                'success' => false,
                'message' => 'Riwayat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $riwayat = Riwayat::find($id);

        if (!$riwayat) {
            return response()->json([
                'success' => false,
                'message' => 'Riwayat tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'sometimes|exists:transaksi,id_transaksi',
            'id_pengguna' => 'sometimes|exists:pengguna,id_pengguna',
            'keterangan' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $riwayat->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil diupdate',
            'data' => $riwayat->load(['transaksi.motor', 'pengguna'])
        ], 200);
    }

    public function destroy($id)
    {
        $riwayat = Riwayat::find($id);

        if (!$riwayat) {
            return response()->json([
                'success' => false,
                'message' => 'Riwayat tidak ditemukan'
            ], 404);
        }

        $riwayat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil dihapus'
        ], 200);
    }

    // Method untuk riwayat berdasarkan pengguna
    public function byUser($id_pengguna)
    {
        $riwayat = Riwayat::with(['transaksi.motor', 'pengguna'])
            ->where('id_pengguna', $id_pengguna)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ], 200);
    }

    // Method untuk riwayat berdasarkan transaksi
    public function byTransaction($id_transaksi)
    {
        $riwayat = Riwayat::with(['transaksi.motor', 'pengguna'])
            ->where('id_transaksi', $id_transaksi)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ], 200);
    }
}