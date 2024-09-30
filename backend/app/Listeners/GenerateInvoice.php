<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Invoice;

class GenerateInvoice
{
    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $order = $event->order;

        // Generate the invoice number
        $invoiceNumber = 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

        // Create the invoice
        Invoice::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'invoice_number' => $invoiceNumber,
            'total_amount' => $order->total_amount,
            'status' => 'pending', // or 'paid' depending on your logic
            'due_date' => now()->addDays(30),
        ]);

        echo "Invoice generated.\n";
    }
}
