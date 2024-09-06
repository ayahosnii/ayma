<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $orders = Order::with('orderItems')->get(); // Retrieve all orders
        return response()->json($orders);
    }

    // Store a newly created resource in storage.
    public function store(CreateOrderRequest $request)
    {
        // Create a new order with validated data, excluding products
        $order = Order::create($request->except('products'));

        // Loop through the products and create order items for each
        foreach ($request->products as $productData) {
            // Retrieve the product based on product_id
            $product = Product::find($productData['product_id']);

            // Check if the product exists
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            // Create an order item with the product details
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
                'total' => $product->price * $productData['quantity'], // Calculate total based on quantity
                'product_name' => $product->name ?? '', // Ensure this is not null
                'product_description' => $product->description ?? '',
            ]);
        }

        return response()->json($order->load('orderItems'), 201); // Return order with items
    }

    // Display the specified resource.
    public function show(Order $order)
    {
        return response()->json($order->load('orderItems')); // Include order items in response
    }

    // Update the specified resource in storage.
    public function update(CreateOrderRequest $request, Order $order)
    {
        // Update the order with validated data
        $order->update($request->except('products'));

        // Optionally, you can update the associated order items here

        return response()->json($order);
    }

    // Remove the specified resource from storage.
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(null, 204);
    }

    public function countOrders()
    {
        $countOrders = Order::count();
        return response()->json(['countOrders' => $countOrders]);
    }
}
