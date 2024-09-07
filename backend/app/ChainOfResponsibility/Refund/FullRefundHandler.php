<?php

namespace App\ChainOfResponsibility\Refund;

class FullRefundHandler extends RefundHandler {
    public function handle($request) {
        // Check if the refund type is 'full'
        if ($request['refund_type'] === 'full') {
            // Logic for processing a full refund
            return response()->json(['message' => 'Full refund processed successfully.'], 200);
        }
        // Pass the request to the next handler in the chain
        return parent::handle($request);
    }
}
