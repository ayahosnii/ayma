<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryTracking implements ShouldBroadcastNow
{
    public $order;

    public function __construct($deliveryId, $validated)
    {
        $this->deliveryId = $deliveryId;
        $this->validated = $validated;
    }

    public function broadcastOn()
    {
        return new Channel("tracking.{$this->deliveryId}");
    }

    public function broadcastWith()
    {
        return [
            'latitude' => $this->validated['latitude'],
            'longitude' => $this->validated['longitude'],
            'last_updated_at' => now(),
        ];
    }
}
