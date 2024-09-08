<?php

namespace App\ChainOfResponsibility\orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserHandler extends AbstractOrderProcessHandler
{
    public function handle($request, Order $order = null)
    {
        // Retrieve or create the user based on the provided user_id
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            // If the user does not exist, create a new user with provided details
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make(Str::random(12)),
            ]);
        }

        // Set the user_id in the request to ensure it is used in subsequent handlers
        $request->merge(['user_id' => $user->id]);

        echo "User handled.\n";

        // Continue to the next handler in the chain
        return parent::handle($request, $order);
    }
}
