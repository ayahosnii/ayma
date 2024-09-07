<?php

namespace App\ChainOfResponsibility\orders;

use App\Models\Order;

abstract class AbstractOrderProcessHandler implements OrderProcessHandler {
    private ?OrderProcessHandler $nextHandler = null;

    public function setNext(OrderProcessHandler $handler): OrderProcessHandler {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle($request, Order $order = null) {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request, $order);
        }
        return $order;
    }
}
