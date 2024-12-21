<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    // Map statuses to current_step values
    public function currentStep(): int
    {
        return match($this) {
            self::Pending => 1,
            self::Processing => 2,
            self::Shipped => 3,
            self::Delivered => 4,
            self::Cancelled => 5,
        };
    }

    // Get the timeline status for the order
    public function timelineEntry(): array
    {
        $statusMessages = [
            'pending' => 'Order Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        // Use the enum's value to get the correct status message
        return [
            'date' => now()->format('D, d M'), // Current date formatted as 'Mon, 21 Dec'
            'status' => $statusMessages[$this->value],  // Use the enum's value
        ];
    }
}
