<?php

namespace App\ChainOfResponsibility\Refund;
class ReviewRequestHandler extends RefundHandler {
    public function handle($request) {
        // Logic to send the refund request for review to the administrators
        if ($this->sendForReview($request)) {
            return parent::handle($request); // Pass the request to the next handler in the chain
        }
        return response()->json(['error' => 'Refund request could not be reviewed'], 400);
    }

    private function sendForReview($request) {
        // Logic to actually send the review request to the administrators
        return true; // Example, replace with actual review logic
    }
}
