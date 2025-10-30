<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarifController extends Controller
{
    public function index()
    {
        $tarif = Tarif::all();
        return response()->json([
            'success' => true,
            'data' => $tarif
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_tarif' => 'required|string|max:50',
            'biaya' => 'required|numeric|min:0',
            'keterangan_tarif' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tarif = Tarif::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil ditambahkan',
            'data' => $tarif
        ], 201);
    }

    public function show($id)
    {
        $tarif = Tarif::find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tarif
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $tarif = Tarif::find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_tarif' => 'sometimes|string|max:50',
            'biaya' => 'sometimes|numeric|min:0',
            'keterangan_tarif' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tarif->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil diupdate',
            'data' => $tarif
        ], 200);
    }

    public function destroy($id)
    {
        $tarif = Tarif::find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        $tarif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil dihapus'
        ], 200);
    }
}