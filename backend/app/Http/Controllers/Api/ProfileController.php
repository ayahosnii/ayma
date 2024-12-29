<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        // Replace this with authenticated user logic if authentication is used
        $userId = $request->user()->id; // Assuming the user is authenticated

        // Fetch the user profile data
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'avatarImg' => $user->avatar ?? asset('images/avatars/avatar-1.png'),
            'name' => $user->name,
            'lastName' => $user->last_name,
            'email' => $user->email,
            'org' => $user->organization ?? 'Not Specified',
            'phone' => $user->phone_number ?? 'Not Specified',
            'address' => $user->address,
            'state' => $user->state,
            'zip' => $user->zip_code,
            'country' => $user->country,
            'language' => $user->language ?? 'English',
            'timezone' => $user->timezone ?? '(GMT+00:00) UTC',
            'currency' => $user->currency ?? 'USD',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'organization' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
}
