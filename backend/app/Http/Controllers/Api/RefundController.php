<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Refund\RefundService;
use App\Services\Refund\RefundStrategies\FullRefundStrategy;
use App\Services\Refund\RefundStrategies\PartialRefundStrategy;
use App\Services\Refund\RefundStrategies\StoreCreditStrategy;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * Handle a refund request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processRefund(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'refund_type' => 'required|string|in:full,partial,store_credit',
            'status' => 'required|string|in:pending,approved',
            'items' => 'sometimes|array',  // Optional, only for partial refunds
            'items.*.product_id' => 'required_if:refund_type,partial|integer|exists:products,id',
            'items.*.quantity' => 'required_if:refund_type,partial|integer|min:1'
        ]);

        $order = Order::find($validated['order_id']);
        $amount = $order->total_amount;
        $type = $validated['refund_type'];
        $items = $validated['items'] ?? [];

        // Determine the strategy based on the refund type
        switch ($type) {
            case 'full':
                $strategy = new FullRefundStrategy();
                break;
            case 'partial':
                if (empty($items)) {
                    return response()->json(['error' => 'Items are required for partial refunds'], 400);
                }
                $strategy = new PartialRefundStrategy($items);
                break;
            case 'store_credit':
                $strategy = new StoreCreditStrategy();
                break;
            default:
                return response()->json(['error' => 'Invalid refund type'], 400);
        }

        // Process the refund using the selected strategy
        $refundService = new RefundService($strategy);
        $refundService->execute($order, $amount/*, $userRole*/, $items);

        return response()->json(['message' => 'Refund processed successfully']);
    }

}
