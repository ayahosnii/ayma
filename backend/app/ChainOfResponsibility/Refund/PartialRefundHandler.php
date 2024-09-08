<?php

namespace App\ChainOfResponsibility\Refund;

class PartialRefundHandler extends RefundHandler {
    public function handle($request) {
        // Check if the refund type is 'partial'
        if ($request['refund_type'] === 'partial') {
            // Logic for processing a partial refund
            return response()->json(['message' => 'Partial refund processed successfully.'], 200);
        }
        // Pass the request to the next handler in the chain
        return parent::handle($request);
    }
}
