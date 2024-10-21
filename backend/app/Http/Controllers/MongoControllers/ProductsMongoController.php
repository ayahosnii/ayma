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
        // Validate the request data (excluding custom fields for now)
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'is_featured'    => 'boolean',
            'category_id'    => 'required|string',
            'additional_attributes' => 'nullable|array',
        ]);

        // Check manually for uniqueness of 'sku' in the MongoDB collection
        $existingProduct = ProductsMongo::where('sku', $validated['sku'])->first();
        if ($existingProduct) {
            return response()->json([
                'message' => 'The SKU has already been taken.',
            ], 422);  // Unprocessable Entity
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
            'additional_attributes'    => $validated['additional_attributes'],
        ];


        // Create and store the product in MongoDB
        $product = ProductsMongo::create($productData);

        // Return a JSON response with the created product
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }
}
