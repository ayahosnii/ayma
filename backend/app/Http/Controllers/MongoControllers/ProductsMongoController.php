<?php

namespace App\Http\Controllers\MongoControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MongoModels\ProductsMongo;

class ProductsMongoController extends Controller
{

    public function index()
    {
        // Retrieve all products from MongoDB
        $products = ProductsMongo::all();

        // Return a JSON response with the list of products
        return response()->json([
            'products' => $products,
        ], 200);  // HTTP status 200 OK
    }

    // Store a new product in MongoDB
    public function store(Request $request)
{
    // Validate the request data
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
        'images.*'       => 'image|mimes:jpeg,png,jpg,gif,svg', // Validate each image
    ]);

    // Check manually for uniqueness of 'sku' in the MongoDB collection
    $existingProduct = ProductsMongo::where('sku', $validated['sku'])->first();
    if ($existingProduct) {
        return response()->json([
            'message' => 'The SKU has already been taken.',
        ], 422);  // Unprocessable Entity
    }

    // Handle image uploads
    $imagePaths = [];
    if (isset($validated['images'])) {
        foreach ($validated['images'] as $image) {
            $path = $image->store('products/images', 'public'); // Store images in the 'public' disk
            $imagePaths[] = $path; // Save the path to the array
        }
    }

    // Merge any additional attributes with the validated fields
    $productData = [
        'name'           => $validated['name'],
        'sku'            => $validated['sku'],
        'price'          => $validated['price'],
        'discount_price' => $validated['discount_price'] ?? null,
        'description'    => $validated['description'] ?? '',
        'stock'          => $validated['stock'],
        'is_featured'    => $validated['is_featured'] ?? false,
        'category_id'    => $validated['category_id'],
        'additional_attributes' => $validated['additional_attributes'],
        'images'         => $imagePaths, // Save image paths
    ];

    // Create and store the product in MongoDB
    $product = ProductsMongo::create($productData);

    // Return a JSON response with the created product
    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
    ], 201);
}

// Update an existing product
public function update(Request $request, $id)
{
    // Validate the request data
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
        'images.*'       => 'image|mimes:jpeg,png,jpg,gif,svg', // Validate each image
    ]);

    // Find the product by ID
    $product = ProductsMongo::findOrFail($id);

    // Handle image uploads if provided
    if (isset($validated['images'])) {
        $imagePaths = [];
        foreach ($validated['images'] as $image) {
            $path = $image->store('products/images', 'public');
            $imagePaths[] = $path;
        }
        $validated['images'] = $imagePaths; // Update images with the new paths
    }

    // Update the product with the validated data
    $product->update($validated);

    // Return a JSON response with the updated product
    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product,
    ], 200);
}

// Delete a product
public function destroy($id)
{
    // Find the product by ID
    $product = ProductsMongo::findOrFail($id);

    // Delete the product
    $product->delete();

    // Return a JSON response indicating success
    return response()->json([
        'message' => 'Product deleted successfully',
    ], 200);
}

}
