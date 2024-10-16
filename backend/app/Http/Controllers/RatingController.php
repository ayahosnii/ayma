<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'user_id'    => 'required',  // MongoDB uses _id
            'product_id' => 'required', // Validate against the product's _id
            'rating'     => 'required|integer|min:1|max:5', // Rating should be between 1 and 5
            'comment'    => 'nullable|string',             // Optional comment
        ]);

        // Create and store the rating in MongoDB
        $rating = Rating::create([
            'user_id'    => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'],
        ]);

        // Return a JSON response
        return response()->json(['message' => 'Rating added successfully', 'rating' => $rating], 201);
    }
}
