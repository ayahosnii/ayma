<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $orders = Order::all(); // Retrieve all orders
        return response()->json($orders);
    }

    // Store a newly created resource in storage.
    public function store(CreateOrderRequest $request)
    {
        // Create a new order with validated data
        $order = Order::create($request->validated());

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
}
