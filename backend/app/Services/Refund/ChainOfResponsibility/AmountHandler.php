<?php

namespace App\Services\Refund\ChainOfResponsibility;

use App\ChainOfResponsibility\Refund\RefundHandler;

class AmountHandler implements RefundHandler
{
    protected $nextHandler;

    public function setNext(RefundHandler $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(array $requestData): bool
    {
        if ($requestData['amount'] <= 0) {
            return false;
        }

        if ($this->nextHandler) {
            return $this->nextHandler->handle($requestData);
        }

        return true;
    }
}
