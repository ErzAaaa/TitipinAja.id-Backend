<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('pengguna')->get();
        return response()->json([
            'success' => true,
            'data' => $members
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal_daftar' => 'required|date',
            'jenis_member' => 'required|string|max:50',
            'diskon_decimal' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $member = Member::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil ditambahkan',
            'data' => $member
        ], 201);
    }

    public function show($id)
    {
        $member = Member::with('pengguna')->find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $member
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_pengguna' => 'sometimes|exists:pengguna,id_pengguna',
            'tanggal_daftar' => 'sometimes|date',
            'jenis_member' => 'sometimes|string|max:50',
            'diskon_decimal' => 'sometimes|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $member->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil diupdate',
            'data' => $member
        ], 200);
    }

    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil dihapus'
        ], 200);
    }
}