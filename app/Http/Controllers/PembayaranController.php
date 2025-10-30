<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')->get();
        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'tgl_pembayaran' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:50',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah transaksi sudah punya pembayaran
        $existingPayment = Pembayaran::where('id_transaksi', $request->id_transaksi)->first();
        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah memiliki pembayaran'
            ], 400);
        }

        $pembayaran = Pembayaran::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditambahkan',
            'data' => $pembayaran->load('transaksi.motor.pengguna')
        ], 201);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')->find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'sometimes|exists:transaksi,id_transaksi',
            'tgl_pembayaran' => 'sometimes|date',
            'jumlah_bayar' => 'sometimes|numeric|min:0',
            'metode_pembayaran' => 'sometimes|string|max:50',
            'status' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $pembayaran->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diupdate',
            'data' => $pembayaran->load('transaksi.motor.pengguna')
        ], 200);
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $pembayaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus'
        ], 200);
    }

    // Method untuk laporan pembayaran hari ini
    public function todayPayments()
    {
        $today = Carbon::today();
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')
            ->whereDate('tgl_pembayaran', $today)
            ->get();

        $total = $pembayaran->sum('jumlah_bayar');

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
            'total_hari_ini' => $total
        ], 200);
    }

    // Method untuk laporan pembayaran berdasarkan periode
    public function reportByPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')
            ->whereBetween('tgl_pembayaran', [$request->tanggal_mulai, $request->tanggal_akhir])
            ->get();

        $total = $pembayaran->sum('jumlah_bayar');

        return response()->json([
            'success' => true,
            'periode' => [
                'mulai' => $request->tanggal_mulai,
                'akhir' => $request->tanggal_akhir
            ],
            'data' => $pembayaran,
            'total_pembayaran' => $total
        ], 200);
    }
}