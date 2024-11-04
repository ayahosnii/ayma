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
        // Validate the request data
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:255|unique:mongodb.products,sku',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'is_featured'    => 'boolean',
            'category_id'    => 'required',
            'additional_attributes' => 'nullable|array',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($validated['images'] as $image) {
                $path = $image->store('products/images', 'public');
                $imagePaths[] = $path;
            }
        }

        // Create a new product with validated data and image paths
        $product = ProductsMongo::create([
            'name'                => $validated['name'],
            'sku'                 => $validated['sku'],
            'price'               => $validated['price'],
            'discount_price'      => $validated['discount_price'] ?? null,
            'description'         => $validated['description'] ?? '',
            'stock'               => $validated['stock'],
            'is_featured'         => $validated['is_featured'] ?? false,
            'category_id'         => $validated['category_id'],
            'additional_attributes' => $validated['additional_attributes'],
            'images'              => $imagePaths,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    // Update an existing product in MongoDB
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'is_featured'    => 'boolean',
            'category_id'    => 'required',
            'additional_attributes' => 'nullable|array',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Find the product by ID
        $product = ProductsMongo::findOrFail($id);

        // Handle image uploads if provided
        $imagePaths = $product->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images from storage if new images are provided
            foreach ($imagePaths as $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
            $imagePaths = [];
            foreach ($validated['images'] as $image) {
                $path = $image->store('products/images', 'public');
                $imagePaths[] = $path;
            }
        }

        // Update product data
        $product->update(array_merge($validated, ['images' => $imagePaths]));

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }

    // Delete a product and its images
    public function destroy($id)
    {
        $product = ProductsMongo::findOrFail($id);

        // Delete images from storage
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Delete the product
        $product->delete();

        return response()->json([
            'message' => 'Product and associated images deleted successfully',
        ], 200);
    }
}
