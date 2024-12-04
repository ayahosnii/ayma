<?php

namespace App\Broadcasting;

use App\Models\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Http\Request;

class StockNotificationChannel implements ShouldBroadcastNow
{
    public $product_id;
    public $product_name;
    public $stock;

    public function __construct($product_id, $product_name, $stock)
    {
        $this->product_id = $product_id;
        $this->product_name = $product_name;
        $this->stock = $stock;
    }

    public function broadcastOn()
    {
        return new Channel('stock-notifications');
    }

    public function broadcastAs()
    {
        return 'stock-low';
    }
}
