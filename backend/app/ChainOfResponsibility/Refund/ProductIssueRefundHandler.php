<?php

namespace App\ChainOfResponsibility\Refund;

class ProductIssueRefundHandler extends RefundHandler {
    public function handle($request) {
        if ($request['refund_type'] === 'product_issue') {
            // منطق معالجة الاسترداد بسبب مشاكل في المنتج
            return response()->json(['message' => 'Refund due to product issue processed successfully.'], 200);
        }
        return parent::handle($request);
    }
}
