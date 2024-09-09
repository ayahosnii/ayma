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
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Junges\Kafka\Facades\Kafka;

class OrderController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $orders = Order::with('orderItems', 'user')->get(); // Retrieve all orders
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

        // Kafka Producer: Send order data to Kafka topic
//        Kafka::publishOn('orders-topic')
//            ->withHeaders(['event' => 'order.created'])
//            ->withBodyKey('order_id', $order->id)
//            ->withBodyKey('total_amount', $order->total_amount)
//            ->send();

        return response()->json($order->load('orderItems'), 201);
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
}
