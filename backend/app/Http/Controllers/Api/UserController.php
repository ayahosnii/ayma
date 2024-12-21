<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\DeliveryShippingCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $role = $request->input('role'); // Get the role from the request

        $query = User::query();

        // If a role is provided, filter by the role
        if ($role) {
            $query->role($role); // `role` is a scope provided by Spatie
        }

        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Store the user data, including avatar image.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(StoreUserRequest $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Handle file upload for avatar
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // Store user in the database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
                'status' => $request->status,
                'timezone' => $request->timezone,
                'language' => $request->language,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'default_currency' => $request->default_currency,
                'avatar' => $avatarPath,
            ]);

            // Assign role to the user (from the validated role)
            $user->assignRole($request->role);

            // Check the role and create the corresponding customer or employee record
            if ($request->role === 'Customer') {
                \App\Models\Customer::create([
                    'user_id' => $user->id,
                    'loyalty_points' => 0,
                ]);
            } elseif ($request->role === 'employee') {
                \App\Models\Employee::create([
                    'user_id' => $user->id,
                    'hire_date' => $request->hire_date ?? null,
                    'job_title' => $request->job_title ?? null,
                    'department' => $request->department ?? null,
                    'salary' => $request->salary ?? null,
                ]);
            }elseif ($request->role === 'Delivery') {
                DeliveryShippingCompany::create([
                    'user_id' => $user->id,
                    'shipping_company_id' => $request->shipping_company,
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            // Optionally log the exception
            Log::error('Error creating user: ' . $e->getMessage());

            return response()->json(['error' => 'An error occurred while creating the user.', 'message' => $e->getMessage()], 500);
        }
    }
}
