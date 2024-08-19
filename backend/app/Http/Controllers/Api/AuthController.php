<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

use Laravel\Passport\PersonalAccessTokenResult;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $tokenResult = $user->createToken('Token Name');

            $data = [
                'accessToken' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'userData' => new UserResource($user)
            ];

            return response()->json($data, 201);
        } catch (\Exception $e) {
            Log::error('Registration Error: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $tokenResult = $user->createToken('Token Name');

        $data = [
            'accessToken' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'userData' => new UserResource($user)
        ];

        return response()->json($data, 200);
    }
}
