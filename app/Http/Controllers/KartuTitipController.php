<?php

namespace App\Http\Controllers;

use App\Models\KartuTitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KartuTitipController extends Controller
{
    public function index()
    {
        $kartu = KartuTitip::with('transaksi.motor.pengguna')->get();
        return response()->json([
            'success' => true,
            'data' => $kartu
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'nomor_kartu' => 'required|string|max:50|unique:kartu_titip,nomor_kartu',
            'status' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah transaksi sudah punya kartu
        $existingCard = KartuTitip::where('id_transaksi', $request->id_transaksi)->first();
        if ($existingCard) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah memiliki kartu titip'
            ], 400);
        }

        $kartu = KartuTitip::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kartu titip berhasil ditambahkan',
            'data' => $kartu->load('transaksi.motor.pengguna')
        ], 201);
    }

    public function show($id)
    {
        $kartu = KartuTitip::with('transaksi.motor.pengguna')->find($id);

        if (!$kartu) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu titip tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kartu
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $kartu = KartuTitip::find($id);

        if (!$kartu) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu titip tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'sometimes|exists:transaksi,id_transaksi',
            'nomor_kartu' => 'sometimes|string|max:50|unique:kartu_titip,nomor_kartu,' . $id . ',id_kartu',
            'status' => 'sometimes|string|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $kartu->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kartu titip berhasil diupdate',
            'data' => $kartu->load('transaksi.motor.pengguna')
        ], 200);
    }

    public function destroy($id)
    {
        $kartu = KartuTitip::find($id);

        if (!$kartu) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu titip tidak ditemukan'
            ], 404);
        }

        $kartu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kartu titip berhasil dihapus'
        ], 200);
    }

    // Method untuk cari kartu berdasarkan nomor
    public function findByNumber($nomor_kartu)
    {
        $kartu = KartuTitip::with('transaksi.motor.pengguna')
            ->where('nomor_kartu', $nomor_kartu)
            ->first();

        if (!$kartu) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kartu
        ], 200);
    }

    // Method untuk kartu yang sedang digunakan
    public function activeCards()
    {
        $kartu = KartuTitip::with('transaksi.motor.pengguna')
            ->where('status', 'digunakan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kartu
        ], 200);
    }

    // Method untuk kartu yang tersedia
    public function availableCards()
    {
        $kartu = KartuTitip::where('status', 'tersedia')->get();

        return response()->json([
            'success' => true,
            'data' => $kartu
        ], 200);
    }
}