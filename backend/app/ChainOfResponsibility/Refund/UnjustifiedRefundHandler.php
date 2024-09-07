<?php

namespace App\ChainOfResponsibility\Refund;

class UnjustifiedRefundHandler extends RefundHandler {
    public function handle($request) {
        if (!$this->isJustified($request)) {
            // منطق رفض الاستردادات غير المبررة
            return response()->json(['error' => 'Refund request is unjustified or not within policy.'], 400);
        }
        return parent::handle($request);
    }

    private function isJustified($request) {
        // منطق التحقق مما إذا كانت الطلبات مبررة أم لا
        return true; // مثال، يجب استبداله بالتحقق الفعلي
    }
}
