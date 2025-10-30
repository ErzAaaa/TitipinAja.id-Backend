<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotorController extends Controller
{
    public function index()
    {
        $motors = Motor::with('pengguna')->get();
        return response()->json([
            'success' => true,
            'data' => $motors
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'merk' => 'required|string|max:50',
            'plat_nomor' => 'required|string|max:20|unique:motor,plat_nomor',
            'warna' => 'required|string|max:30',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $motor = Motor::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil ditambahkan',
            'data' => $motor
        ], 201);
    }

    public function show($id)
    {
        $motor = Motor::with('pengguna')->find($id);

        if (!$motor) {
            return response()->json([
                'success' => false,
                'message' => 'Motor tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $motor
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $motor = Motor::find($id);

        if (!$motor) {
            return response()->json([
                'success' => false,
                'message' => 'Motor tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_pengguna' => 'sometimes|exists:pengguna,id_pengguna',
            'merk' => 'sometimes|string|max:50',
            'plat_nomor' => 'sometimes|string|max:20|unique:motor,plat_nomor,' . $id . ',id_motor',
            'warna' => 'sometimes|string|max:30',
            'tahun' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $motor->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil diupdate',
            'data' => $motor
        ], 200);
    }

    public function destroy($id)
    {
        $motor = Motor::find($id);

        if (!$motor) {
            return response()->json([
                'success' => false,
                'message' => 'Motor tidak ditemukan'
            ], 404);
        }

        $motor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil dihapus'
        ], 200);
    }
}