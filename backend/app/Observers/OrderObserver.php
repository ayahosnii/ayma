<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Delivery;
use App\Enums\OrderStatus;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function created(Order $order)
    {
        // Create a new Delivery record when an Order is created
        $delivery = new Delivery();
        $delivery->order_id = $order->id;
        $delivery->current_step = OrderStatus::Pending->currentStep(); // Start with the first step
        $delivery->tracking_code = uniqid('track_'); // Generate a unique tracking code
        $delivery->delivery_partner = 'Default Partner'; // Placeholder for actual partner logic

        // Initialize the timeline with steps but without dates for upcoming steps
        $timeline = [
            [
                'date' => '',  // No date yet for "Order Confirmed"
                'status' => 'Order Confirmed',
            ],
            [
                'date' => '',  // No date yet for "Processing"
                'status' => 'Processing',
            ],
            [
                'date' => '',  // No date yet for "Shipped"
                'status' => 'Shipped',
            ],
            [
                'date' => '',  // No date yet for "Delivered"
                'status' => 'Delivered',
            ],
        ];

        // Update the timeline for each step that has been reached
        foreach ($timeline as $index => $entry) {
            // Check if the order has reached the current step
            if ($order->status == $entry['status']) {
                // Set the date for the current status
                $timeline[$index]['date'] = now()->format('D, d M');
            }
        }

        // Set the timeline in the delivery object
        $delivery->timeline = $timeline;  // Directly assign the timeline array without JSON encoding
        $delivery->save(); // Save the new delivery record
    }

    public function updated(Order $order)
    {
        // Check if the status has been updated
        if ($order->isDirty('status')) {
            // Get the corresponding delivery for this order
            $delivery = Delivery::where('order_id', $order->id)->first();

            if ($delivery) {
                // Use the OrderStatus enum to get the current step
                $status = OrderStatus::from($order->status);
                $delivery->current_step = $status->currentStep();

                // Update the timeline (optional)
                $timelineEntry = $status->timelineEntry();

                // The timeline is already an array, no need to decode
                $timeline = $delivery->timeline ?: [];  // Default to an empty array if timeline is null

                // Check if the status already exists in the timeline and update if necessary
                $updated = false;
                foreach ($timeline as &$timelineItem) {
                    if ($timelineItem['status'] === $timelineEntry['status']) {
                        // Update the existing entry's date
                        $timelineItem['date'] = now()->format('D, d M');
                        $updated = true;
                        break;  // Exit loop after updating
                    }
                }

                // If no existing status was found, add the new entry
                if (!$updated) {
                    $timeline[] = $timelineEntry;
                }

                // Update the timeline in the delivery record
                $delivery->timeline = $timeline;

                // Save the changes to the delivery
                $delivery->save();
            }
        }
    }
}
