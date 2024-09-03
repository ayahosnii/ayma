<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderItemRequest;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $orderItems = OrderItem::all(); // Retrieve all order items
        return response()->json($orderItems);
    }


    // Store a newly created resource in storage.
    public function store(CreateOrderItemRequest $request)
    {
        // Create a new order item with validated data
        $orderItem = OrderItem::create($request->validated());

        return response()->json($orderItem, 201);
    }

    // Display the specified resource.
    public function show(OrderItem $orderItem)
    {
        return response()->json($orderItem);
    }

    // Update the specified resource in storage.
    public function update(CreateOrderItemRequest $request, OrderItem $orderItem)
    {
        // Update the order item with validated data
        $orderItem->update($request->validated());

        return response()->json($orderItem);
    }

    // Remove the specified resource from storage.
    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return response()->json(null, 204);
    }
}
