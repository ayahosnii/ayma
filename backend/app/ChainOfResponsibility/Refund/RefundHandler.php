<?php

namespace App\ChainOfResponsibility\Refund;

abstract class RefundHandler {
    protected $nextHandler;

    public function setNext(RefundHandler $handler) {
        $this->nextHandler = $handler;
        return $this;
    }

    public function handle($request) {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }
        return response()->json(['error' => 'Refund request could not be processed'], 400);
    }
}
