<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('color', 'size', 'category', 'primaryImage', 'images')->get(); // Include category relationship
        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        // Validate and get the data
        $validatedData = $request->validated();

        // Create the product
        $product = Product::create($validatedData);

        // Check if there are images in the request
        if ($request->hasFile('images')) {
            // Loop through each image
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product_images', 'public');

                // Create a record for each image
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0, // Set the first image as primary
                ]);
            }
        }

        // Return response
        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product->load('color', 'size', 'category', 'primaryImage', 'images') // Load relationships
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProductRequest $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        // Update product details
        $product->update($request->validated());

        // Handle images if provided
        if ($request->hasFile('image')) {
            // Delete the old primary image if exists
            if ($product->primaryImage) {
                Storage::disk('public')->delete($product->primaryImage->image_path);
                $product->primaryImage->delete();
            }

            // Store the new image
            $path = $request->file('image')->store('product_images', 'public');

            // Create a new primary image record
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product->load('color', 'size', 'category', 'primaryImage', 'images') // Load relationships
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        // Delete the associated images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }

    public function countProducts()
    {
        $countProducts = Product::count();
        return response()->json(['countProducts' => $countProducts]);
    }

    public function getProducts()
    {
        $countProducts = Product::get();
        return response()->json($countProducts);
    }
}
