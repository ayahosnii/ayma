<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class OrderCreated implements ShouldBroadcastNow
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('orders'); // Channel name
    }

    public function broadcastWith()
    {

        $userName = $this->order->user->name ?? 'Unknown User'; // Fallback if no user exists

        return [
            'message' => "Order #{$this->order->order_number} Created!",
            'total_amount' => $this->order->total_amount,
            'user_name' => $userName,
        ];
    }
}
