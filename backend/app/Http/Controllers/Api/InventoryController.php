<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\SupplierProduct;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory updates.
     */
    public function index()
    {
        $inventoryUpdates = SupplierProduct::with(['supplier', 'product'])
            ->paginate(10);

        return response()->json($inventoryUpdates);
    }

    /**
     * Store a newly created inventory update.
     */
    public function store(Request $request)
    {
        // Validate request inputs
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
        ]);

        // Create inventory update record
        $inventoryUpdate = SupplierProduct::create($request->all());

        // Update the product's stock
        $product = Product::find($request->product_id);
        if ($product) {
            $product->stock += $request->quantity;
            $product->save();
        }

        return response()->json([
            'message' => 'Inventory update added successfully, stock updated!',
            'data' => $inventoryUpdate
        ], 201);
    }


    /**
     * Show the specified inventory update.
     */
    public function show($id)
    {
        $inventoryUpdate = SupplierProduct::with(['supplier', 'product'])->findOrFail($id);

        return response()->json($inventoryUpdate);
    }

    /**
     * Update the specified inventory update.
     */
    /**
     * Update the specified inventory update.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventoryUpdate = SupplierProduct::findOrFail($id);
        $oldQuantity = $inventoryUpdate->quantity; // Store the previous quantity

        DB::transaction(function () use ($inventoryUpdate, $request, $oldQuantity) {
            // Update the inventory
            $inventoryUpdate->update($request->all());

            // Update product stock
            $product = Product::find($request->product_id);
            if ($product) {
                $quantityDifference = $request->quantity - $oldQuantity;
                $product->stock += $quantityDifference;

                if ($product->stock < 0) {
                    throw new \Exception('Stock cannot be negative!');
                }

                $product->save();
            }
        });

        return response()->json([
            'message' => 'Inventory update updated successfully, stock adjusted!',
            'data' => $inventoryUpdate
        ]);
    }


    /**
     * Remove the specified inventory update.
     */
    public function destroy($id)
    {
        $inventoryUpdate = SupplierProduct::findOrFail($id);

        DB::transaction(function () use ($inventoryUpdate) {
            // Update product stock
            $product = Product::find($inventoryUpdate->product_id);
            if ($product) {
                $product->stock -= $inventoryUpdate->quantity;

                if ($product->stock < 0) {
                    throw new \Exception('Stock cannot be negative!');
                }

                $product->save();
            }

            // Delete the inventory update
            $inventoryUpdate->delete();
        });

        return response()->json(['message' => 'Inventory update deleted successfully, stock adjusted!']);
    }

    public function countInvetoryRecords()
    {
        $countInvetoryRecords = SupplierProduct::count();
        return response()->json(['countInvetoryRecords' => $countInvetoryRecords]);
    }

}

