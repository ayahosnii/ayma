<?php

namespace App\Listeners;

use App\Events\OrderDataRecived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncOrderData implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param OrderDataRecived $event
     * @return void
     */
    public function handle(OrderDataRecived $event): void
    {
        // Extract the order data from the event
        $data = json_decode($event->data);

        // Log the order data. Adjust according to your actual data structure.
        if (isset($data->order_id)) {
            Log::info('Order data synchronized: Order ID ' . $data->order_id);
        } else {
            Log::warning('Order data received does not have an order ID.');
        }
    }
}
