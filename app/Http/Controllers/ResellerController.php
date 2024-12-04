<?php

// app/Http/Controllers/ResellerController.php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResellerController extends Controller
{
    // Menambahkan reseller
    public function store(Request $req)
    {
        // Validasi input
        $rules = [
            'name' => 'required|string',
            'birthdate' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|unique:resellers',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'profile_photo' => 'nullable|string', // Foto profil
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Ambil user yang sedang login
        $user = auth()->user();

        // Simpan reseller baru
        $reseller = Reseller::create([
            'name' => $req->name,
            'birthdate' => $req->birthdate,
            'gender' => $req->gender,
            'phone' => $req->phone,
            'address' => $req->address,
            'latitude' => $req->latitude,
            'longitude' => $req->longitude,
            'profile_photo' => $req->profile_photo, // Simpan foto profil
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Reseller berhasil ditambahkan',
            'reseller' => $reseller
        ], 201);
    }

    // Menampilkan semua reseller milik user yang login
    public function index()
    {
        $user = auth()->user();
        $resellers = $user->resellers;

        return response()->json($resellers, 200);
    }

    // Menampilkan reseller berdasarkan ID
    public function show($id)
    {
        $user = auth()->user();
        $reseller = Reseller::where('id', $id)->where('user_id', $user->id)->first();

        if (!$reseller) {
            return response()->json(['message' => 'Reseller tidak ditemukan atau tidak dimiliki oleh user ini'], 404);
        }

        return response()->json($reseller, 200);
    }

    // Mengupdate reseller
    public function update(Request $req, $id)
    {
        $user = auth()->user();
        $reseller = Reseller::where('id', $id)->where('user_id', $user->id)->first();

        if (!$reseller) {
            return response()->json(['message' => 'Reseller tidak ditemukan atau tidak dimiliki oleh user ini'], 404);
        }

        // Validasi input untuk update
        $rules = [
            'name' => 'sometimes|required|string',
            'birthdate' => 'sometimes|required|date',
            'gender' => 'sometimes|required|in:male,female,other',
            'phone' => 'sometimes|required|string|unique:resellers,phone,' . $reseller->id,
            'address' => 'sometimes|required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'profile_photo' => 'nullable|string', // Aturan untuk foto profil
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $reseller->update($req->only([
            'name', 'birthdate', 'gender', 'phone', 'address', 'latitude', 'longitude', 'profile_photo'
        ]));

        return response()->json([
            'message' => 'Reseller berhasil diperbarui',
            'reseller' => $reseller
        ], 200);
    }

    // Menghapus reseller
    public function destroy($id)
    {
        $user = auth()->user();
        $reseller = Reseller::where('id', $id)->where('user_id', $user->id)->first();

        if (!$reseller) {
            return response()->json(['message' => 'Reseller tidak ditemukan atau tidak dimiliki oleh user ini'], 404);
        }

        $reseller->delete();

        return response()->json(['message' => 'Reseller berhasil dihapus'], 200);
    }
}
