<?php

namespace App\Services\Refund\ChainOfResponsibility;

class StaffRoleHandler implements RefundHandler
{
    protected $nextHandler;
    protected $userRole;

    public function __construct(string $userRole)
    {
        $this->userRole = $userRole;
    }

    public function setNext(RefundHandler $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(array $requestData): bool
    {
        // Example: Only managers can approve refunds above $100
        if ($requestData['amount'] > 100 && $this->userRole !== 'manager') {
            return false;
        }

        if ($this->nextHandler) {
            return $this->nextHandler->handle($requestData);
        }

        return true;
    }
}
