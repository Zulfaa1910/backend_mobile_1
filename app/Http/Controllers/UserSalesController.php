<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\UserSales;
use Illuminate\Http\Request;

class UserSalesController extends Controller
{
    // Register user
    public function register(Request $req)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:user_sales',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|unique:user_sales',
            'birthdate' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $merk_hp = $this->getDeviceInfo($req);
        $verification_code = Str::random(6);
        $kode_unik = $this->generateKodeUnik();
        $kode_sales = $this->generateKodeSales();

        $user = UserSales::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'phone' => $req->phone,
            'verification_code' => $verification_code,
            'birthdate' => $req->birthdate,
            'gender' => $req->gender,
            'address' => $req->address,
            'kode_unik' => $kode_unik,
            'kode_sales' => $kode_sales,
            'merk_hp' => $merk_hp
        ]);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    private function getDeviceInfo(Request $req)
    {
        $userAgent = $req->header('User-Agent');
        if (preg_match('/\((.*?)\)/', $userAgent, $matches)) {
            return $matches[1];
        }
        return 'Unknown Device';
    }

    private function generateKodeUnik()
    {
        return 'SL' . time() . Str::random(4);
    }

    private function generateKodeSales()
    {
        $lastUser = UserSales::orderBy('kode_sales', 'desc')->first();
        if (!$lastUser) {
            return 'SL000001';
        }
        $lastKodeSales = $lastUser->kode_sales;
        $lastNumber = intval(substr($lastKodeSales, 2));
        $newNumber = $lastNumber + 1;
        return 'SL' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Login user
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = UserSales::where('email', $req->email)->first();

        if ($user && Hash::check($req->password, $user->password)) {
            if (!$user->phone_verified_at) {
                return response()->json(['message' => 'Phone number not verified.'], 403);
            }

            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['message' => 'Incorrect email or password'], 401);
    }

    // Verify phone
    public function verifyPhone(Request $req)
    {
        $req->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|string'
        ]);

        $user = UserSales::where('phone', $req->phone)
            ->where('verification_code', $req->verification_code)
            ->first();

        if ($user) {
            $user->phone_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return response()->json(['message' => 'Phone verified successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid verification code.'], 400);
    }

    // CRUD UserSales
    public function index()
    {
        $users = UserSales::all();
        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = UserSales::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user, 200);
    }

    public function update(Request $req, $id)
    {
        $user = UserSales::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $rules = [
            'name' => 'sometimes|string',
            'email' => 'sometimes|string|email|unique:user_sales,email,' . $id,
            'phone' => 'sometimes|string|unique:user_sales,phone,' . $id,
            'birthdate' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'address' => 'sometimes|string',
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user->update($req->only([
            'name', 'email', 'phone', 'birthdate', 'gender', 'address'
        ]));

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    public function destroy($id)
    {
        $user = UserSales::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    // Logout
    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
