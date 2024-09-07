<?php

namespace App\ChainOfResponsibility\Refund;

use App\Models\Product;
use Carbon\Carbon;

class ReturnEligibilityHandler extends RefundHandler {
    public function handle($request) {
        // Check if the product is eligible for return
        if ($this->isEligibleForReturn($request)) {
            return parent::handle($request); // Pass the request to the next handler in the chain
        }
        return response()->json(['error' => 'Product not eligible for return'], 400);
    }

    private function isEligibleForReturn($request) {
        // Fetch product details using product ID
        $product = $this->getProduct($request['product_id']);

        // Example logic to determine return eligibility
        if (!$product) {
            return false; // Product not found
        }

        // Check if the product type allows returns
        if (!$product->can_be_returned) {
            return false; // Product type does not allow returns
        }

        // Check if the return request is within the allowed time frame
        $orderDate = new Carbon($request['order_date']); // Use order_date as the purchase date
        $currentDate = Carbon::now();
        $returnDeadline = $orderDate->addDays($product->return_period); // Assuming return_period is in days

        if ($currentDate->gt($returnDeadline)) {
            return false; // Return request is beyond the allowable return period
        }

        return true; // Product is eligible for return
    }

    private function getProduct($productId) {
        // Fetch product details by ID
        return Product::find($productId);
    }
}
