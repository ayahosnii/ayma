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
}
