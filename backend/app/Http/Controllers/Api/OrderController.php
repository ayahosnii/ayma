<?php

namespace App\Http\Controllers\Api;

use App\ChainOfResponsibility\orders\CalculateTotalHandler;
use App\ChainOfResponsibility\orders\CreateOrderHandler;
use App\ChainOfResponsibility\orders\CreateUserHandler;
use App\ChainOfResponsibility\orders\ProcessOrderItemsHandler;
use App\ChainOfResponsibility\Refund\FullRefundHandler;
use App\ChainOfResponsibility\Refund\PartialRefundHandler;
use App\ChainOfResponsibility\Refund\ProductIssueRefundHandler;
use App\ChainOfResponsibility\Refund\ReturnEligibilityHandler;
use App\ChainOfResponsibility\Refund\UnjustifiedRefundHandler;
use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Junges\Kafka\Facades\Kafka;

class OrderController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $orders = Order::with('orderItems', 'user')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($orders);
    }

    // Store a newly created resource in storage.
    public function store(CreateOrderRequest $request)
{
    // Create the handlers
    $createUser = new CreateUserHandler();
    $createOrder = new CreateOrderHandler();
    $processItems = new ProcessOrderItemsHandler();
    $calculateTotal = new CalculateTotalHandler();

    // Chain the handlers together: Create User -> Create Order -> Process Items -> Calculate Total
    $createUser->setNext($createOrder)
        ->setNext($processItems)
        ->setNext($calculateTotal);

    // Start the chain with the request
    $order = $createUser->handle($request);

    // Return the order with items
    if ($order instanceof \Illuminate\Http\JsonResponse) {
        return $order;  // In case any handler returns an error response
    }

    // Update the stock of products
    $this->updateProductStock($request->products);


    broadcast(new OrderCreated($order));

    // Kafka Producer: Send order data to Kafka topic (optional)
    // Kafka::publishOn('orders-topic')
    //     ->withHeaders(['event' => 'order.created'])
    //     ->withBodyKey('order_id', $order->id)
    //     ->withBodyKey('total_amount', $order->total_amount)
    //     ->send();

    return response()->json($order->load('orderItems'), 201);
}

    // Function to update the product stock
    protected function updateProductStock($products)
    {
        foreach ($products as $productData) {
            $product = Product::find($productData['product_id']);

            // Check if enough stock is available before updating
            if ($product->stock >= $productData['quantity']) {
                $product->stock -= $productData['quantity']; // Reduce stock
                $product->save();

                $this->updateSalesMetrics($productData['product_id'], $productData['quantity'], $product->price);
            } else {
                // If stock is insufficient, handle this case
                throw new \Exception('Not enough stock for product: ' . $product->name);
            }
        }
    }

    // Function to update sales metrics
    protected function updateSalesMetrics($productId, $quantity, $price)
    {
        // Find or create a sales metrics record for the product
        $salesMetric = \App\Models\SalesMetric::firstOrCreate(
            ['product_id' => $productId],
            ['units_sold' => 0, 'revenue' => 0]
        );

        // Update units sold and revenue
        $salesMetric->units_sold += $quantity;
        $salesMetric->revenue += $quantity * $price;
        $salesMetric->save();
    }
    // Display the specified resource.
    public function show(Order $order)
    {
        return response()->json($order->load('orderItems')); // Include order items in response
    }

    public function update(CreateOrderRequest $request, Order $order)
    {
        // Update the order with validated data, excluding specific fields
        $order->update($request->except(['id', 'order_number']));

        // Handle updating or removing order items and calculate total amount
        $orderItems = $request->input('order_items', []);
        $totalAmount = $this->updateOrderItems($order, $orderItems);

        // Update the total amount of the order
        $order->update(['total_amount' => $totalAmount]);

        return response()->json($order);
    }

    protected function updateOrderItems(Order $order, array $items)
{
    // Initialize total amount
    $totalAmount = 0;

    // Check if the order has any existing items to update their stock
    $existingItems = $order->orderItems;

    // Step 1: Restore stock for existing items before updating
    foreach ($existingItems as $existingItem) {
        $product = Product::find($existingItem->product_id);
        if ($product) {
            // Restore the stock for the product (as if the original order was removed)
            $product->stock += $existingItem->quantity;
            $product->save();
        }
    }

    // Step 2: Remove all existing items (clean the order for the new items)
    $order->orderItems()->delete();

    // If no items are passed, delete the order and return
    if (empty($items)) {
        $order->delete();
        return 0;
    }

    // Step 3: Add new items and calculate the total
    foreach ($items as $item) {
        // Find the product for the new item
        $product = Product::find($item['product_id']);

        if ($product && $product->stock >= $item['quantity']) {
            // Create the new item for the order
            $orderItem = $order->orderItems()->create($item);

            // Calculate total for each item (quantity * price)
            $itemTotal = $orderItem->quantity * $orderItem->price;

            // Add item total to the overall total amount
            $totalAmount += $itemTotal;

            // Update the total for the order item
            $orderItem->update(['total' => $itemTotal]);

            // Step 4: Decrease the stock based on the new quantities
            $product->stock -= $orderItem->quantity;
            $product->save();
        } else {
            throw new \Exception('Not enough stock for product: ' . $product->name);
        }
    }

    // Return the new total amount for the order
    return $totalAmount;
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

    public function processRefund(Request $request) {
        // Create handlers for processing the refund request
        $returnEligibilityHandler = new ReturnEligibilityHandler();
        $fullRefundHandler = new FullRefundHandler();
        $partialRefundHandler = new PartialRefundHandler();
        $productIssueRefundHandler = new ProductIssueRefundHandler();
        $unjustifiedRefundHandler = new UnjustifiedRefundHandler();

        // Chain the handlers together: Return Eligibility -> Full Refund -> Partial Refund -> Product Issue Refund -> Unjustified Refund
        $returnEligibilityHandler->setNext($fullRefundHandler)
            ->setNext($partialRefundHandler)
            ->setNext($productIssueRefundHandler)
            ->setNext($unjustifiedRefundHandler);

        // Start processing the refund request
        $response = $returnEligibilityHandler->handle($request->all());

        return $response;
    }

    // Method to update the status of an order
    public function updateStatus(Request $request, $id)
{
    // Validate the incoming data to make sure the status is one of the valid values
    $validated = $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
    ]);

    // Find the order by ID (or fail if not found)
    $order = Order::findOrFail($id);

    // Update the status
    $order->status = $validated['status'];
    $order->save();  // Save the changes to the database

    // Return a response indicating success
    return response()->json(['message' => 'Order status updated successfully!'], 200);
}

}
