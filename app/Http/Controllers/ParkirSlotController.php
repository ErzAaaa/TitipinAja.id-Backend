<?php

namespace App\Http\Controllers;

use App\Models\ParkirSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParkirSlotController extends Controller
{
    public function index()
    {
        $slots = ParkirSlot::all();
        return response()->json([
            'success' => true,
            'data' => $slots
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_slot' => 'required|string|max:20|unique:parkir_slot,kode_slot',
            'lokasi' => 'required|string|max:50',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $slot = ParkirSlot::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Slot parkir berhasil ditambahkan',
            'data' => $slot
        ], 201);
    }

    public function show($id)
    {
        $slot = ParkirSlot::find($id);

        if (!$slot) {
            return response()->json([
                'success' => false,
                'message' => 'Slot parkir tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $slot
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $slot = ParkirSlot::find($id);

        if (!$slot) {
            return response()->json([
                'success' => false,
                'message' => 'Slot parkir tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_slot' => 'sometimes|string|max:20|unique:parkir_slot,kode_slot,' . $id . ',id_slot',
            'lokasi' => 'sometimes|string|max:50',
            'status' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $slot->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Slot parkir berhasil diupdate',
            'data' => $slot
        ], 200);
    }

    public function destroy($id)
    {
        $slot = ParkirSlot::find($id);

        if (!$slot) {
            return response()->json([
                'success' => false,
                'message' => 'Slot parkir tidak ditemukan'
            ], 404);
        }

        $slot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slot parkir berhasil dihapus'
        ], 200);
    }

    // Method tambahan untuk cek slot kosong
    public function availableSlots()
    {
        $slots = ParkirSlot::where('status', 'kosong')->get();
        return response()->json([
            'success' => true,
            'data' => $slots
        ], 200);
    }
}