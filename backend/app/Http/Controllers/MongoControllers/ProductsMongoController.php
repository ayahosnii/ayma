<?php

namespace App\Http\Controllers\MongoControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MongoModels\ProductsMongo;
use Illuminate\Support\Facades\Storage;

class ProductsMongoController extends Controller
{
    // Get all products from MongoDB
    public function index()
    {
        $products = ProductsMongo::with('category')->get();

        return response()->json([
            'products' => $products,
        ], 200);
    }

    // Store a new product in MongoDB
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:mongodb.products,sku',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'category_id' => 'required',
            'additional_attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($validated['images'] as $image) {
                $path = $image->store('products/images', 'public');
                $imagePaths[] = $path;
            }
        }

        $product = ProductsMongo::create(array_merge($validated, ['images' => $imagePaths]));

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    // Update an existing product in MongoDB
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'category_id' => 'required',
            'additional_attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product = ProductsMongo::findOrFail($id);

        if ($request->hasFile('images')) {
            foreach ($validated['images'] as $image) {
                $path = $image->store('products/images', 'public');
                $product->images[] = $path;
            }
        }

        $product->update(array_merge($validated, ['images' => $product->images]));

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }

    // Delete a product and its images
    public function destroy($id)
    {
        $product = ProductsMongo::findOrFail($id);

        if ($product->images) {
            foreach ($product->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->delete();

        return response()->json([
            'message' => 'Product and associated images deleted successfully',
        ], 200);
    }

    // Delete a specific image
    public function deleteImage($id, Request $request)
    {
        $product = ProductsMongo::findOrFail($id);
        $imageToDelete = $request->image;

        if (($key = array_search($imageToDelete, $product->images)) !== false) {
            Storage::disk('public')->delete($imageToDelete);
            unset($product->images[$key]);
            $product->images = array_values($product->images);
            $product->save();
        }

        return response()->json([
            'message' => 'Image deleted successfully',
            'product' => $product,
        ], 200);
    }


    public function countProductsMongo()
    {
        $countProductsMongo = ProductsMongo::count();
        return response()->json(['countProductsMongo' => $countProductsMongo]);
    }
}
