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
        $categories = Category::with('children', 'parent')->get();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(CreateCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Store the image in a directory and get the path
            $data['image'] = $request->file('image')->store('category_images', 'public');
        }

        $category = Category::create($data);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function update(CreateCategoryRequest $request, $id)
    {
        // Find the category by ID
        $category = Category::findOrFail($id);

        // Validate the request data
        $data = $request->validated();

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            // Store the new image and update the data array
            $data['image'] = $request->file('image')->store('category_images', 'public');
        }

        // Update the category with the validated data
        $category->update($data);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }

    /**
 * Remove the specified category from the database.
 */
public function destroy($id)
{
    // Find the category by ID
    $category = Category::findOrFail($id);

    // Check if the category has an image and delete it from storage
    if ($category->image) {
        Storage::disk('public')->delete($category->image);
    }

    // Delete the category from the database
    $category->delete();

    return response()->json(['message' => 'Category deleted successfully'], 200);
}


    public function count()
    {
        $count = Category::count();
        return response()->json(['count' => $count]);
    }
}
