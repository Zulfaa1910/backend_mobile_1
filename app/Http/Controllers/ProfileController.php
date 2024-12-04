<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // Create a new profile for the authenticated user
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'alamat' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tanggal_lahir' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create a new profile
        $profile = new UserProfile($request->except(['foto']));
        $profile->user_id = $user->id;

        // If there is a file for 'foto', handle the upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('profile_pictures', 'public');
            $profile->foto = $path;
        }

        $profile->save();

        return response()->json(['message' => 'Profile created successfully', 'profile' => $profile], 201);
    }

    // Existing methods: show, update, destroy
    public function show()
    {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile, 200);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $validator = Validator::make($request->all(), [
            'alamat' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tanggal_lahir' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // If there is a file for 'foto', handle the upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('profile_pictures', 'public');
            $profile->foto = $path;
        }

        $profile->update($request->except(['foto', 'user_id']));

        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile], 200);
    }

    public function destroy()
    {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->delete();

        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }
}
