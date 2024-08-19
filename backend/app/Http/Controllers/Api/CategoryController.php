<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::paginate(3);
        return response()->json($category, 200);
    }

    public function store(CreateCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }
}
