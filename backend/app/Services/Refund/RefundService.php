<?php

namespace App\Services\Refund;

use App\Models\Order;
use App\Services\Refund\ChainOfResponsibility\AmountHandler;
use App\Services\Refund\ChainOfResponsibility\ProductTypeHandler;
use App\Services\Refund\ChainOfResponsibility\StaffRoleHandler;
use App\Services\Refund\RefundStrategies\RefundStrategy;

class RefundService
{
    protected $strategy;

    /**
     * Create a new RefundService instance.
     *
     * @param RefundStrategy $strategy
     */
    public function __construct(RefundStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Execute the refund process.
     *
     * @param Order $order
     * @param float $amount
     * @param string $userRole
     * @param array $items
     * @return void
     */
    public function execute($order, float $amount/*, string $userRole*/, array $items)
    {
        // Create the chain of responsibility
//        $amountHandler = new AmountHandler();
//        $staffRoleHandler = new StaffRoleHandler($userRole);
        $productTypeHandler = new ProductTypeHandler();

//        $amountHandler->setNext($staffRoleHandler);
//        $staffRoleHandler->setNext($productTypeHandler);

        // Build request data
        $requestData = [
            'amount' => $amount,
            'items' => $items
        ];
//
//        // Handle the request through the chain
//        if (!$amountHandler->handle($requestData)) {
//            throw new \Exception('Refund request is not valid.');
//        }

        // Process the refund using the strategy
        $this->strategy->processRefund($order, $amount);
    }
}
