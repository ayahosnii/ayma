<?php

namespace App\Listeners;

use App\Events\OrderCreated;

class UpdateInventory
{
    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        // Access the order via $event->order
        foreach ($event->order->orderItems as $item) {
            $product = $item->product;
            $product->stock -= $item->quantity;  // Decrease stock
            $product->save();
        }

        echo "Inventory updated.\n";
    }
}
