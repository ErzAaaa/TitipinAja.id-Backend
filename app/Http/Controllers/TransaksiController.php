<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ParkirSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])->get();
        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_motor' => 'required|exists:motor,id_motor',
            'id_petugas' => 'required|exists:petugas,id_petugas',
            'id_tarif' => 'required|exists:tarif,id_tarif',
            'id_slot' => 'nullable|exists:parkir_slot,id_slot',
            'jam_masuk' => 'required|date_format:H:i:s',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create($request->all());

            // Update status slot jadi terisi
            if ($request->id_slot) {
                ParkirSlot::where('id_slot', $request->id_slot)
                    ->update(['status' => 'terisi']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan',
                'data' => $transaksi->load(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_motor' => 'sometimes|exists:motor,id_motor',
            'id_petugas' => 'sometimes|exists:petugas,id_petugas',
            'id_tarif' => 'sometimes|exists:tarif,id_tarif',
            'id_slot' => 'nullable|exists:parkir_slot,id_slot',
            'jam_masuk' => 'sometimes|date_format:H:i:s',
            'jam_keluar' => 'nullable|date_format:H:i:s',
            'status' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $transaksi->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaksi->load(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])
        ], 200);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Kembalikan status slot jadi kosong
            if ($transaksi->id_slot) {
                ParkirSlot::where('id_slot', $transaksi->id_slot)
                    ->update(['status' => 'kosong']);
            }

            $transaksi->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk checkout (keluar parkir)
    public function checkout(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jam_keluar' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $transaksi->update([
                'jam_keluar' => $request->jam_keluar,
                'status' => 'selesai'
            ]);

            // Kosongkan slot parkir
            if ($transaksi->id_slot) {
                ParkirSlot::where('id_slot', $transaksi->id_slot)
                    ->update(['status' => 'kosong']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'data' => $transaksi->load(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal checkout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk transaksi aktif
    public function activeTransactions()
    {
        $transaksi = Transaksi::with(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])
            ->where('status', 'aktif')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }
}