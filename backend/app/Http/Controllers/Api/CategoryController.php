<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a paginated list of categories.
     */
    public function index()
    {
        // Optionally, include filtering or sorting
        $categories = Category::with('children')->get();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(CreateCategoryRequest $request)
    {
        $data = $request->validated();

        $data['parent_id'] = $data['parent_id'] ?? null;

        if ($request->hasFile('image')) {
            // Store the image in a directory and get the path
            $data['image'] = $request->file('image')->store('category_images', 'public');
        }

        $category = Category::create($data);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function count()
    {
        $count = Category::count();
        return response()->json(['count' => $count]);
    }
}
