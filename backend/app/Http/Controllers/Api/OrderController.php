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
        // Create a new order with validated data
        $order = Order::create($request->validated());

        // Retrieve the product based on product_id
        $product = Product::find($request->product_id);

        // Check if product exists
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Create an order item with the product details
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $request->product_id,
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
            'product_name' => $product->name ?? '', // Ensure this is not null
            'product_description' => $product->description ?? '',
        ]);

        return response()->json($order, 201);
    }

    // Display the specified resource.
    public function show(Order $order)
    {
        return response()->json($order);
    }

    // Update the specified resource in storage.
    public function update(CreateOrderRequest $request, Order $order)
    {
        // Update the order with validated data
        $order->update($request->validated());

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
