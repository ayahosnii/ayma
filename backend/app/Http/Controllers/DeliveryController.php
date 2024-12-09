<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'tracking_code' => 'required|unique:deliveries,tracking_code',
        'delivery_partner' => 'required|string|max:255',
    ]);

    $delivery = Delivery::create([
        'order_id' => $validated['order_id'],
        'tracking_code' => $validated['tracking_code'],
        'delivery_partner' => $validated['delivery_partner'],
        'timeline' => [
            ['step' => 'Order Placed', 'timestamp' => now()],
        ],
    ]);

    return response()->json($delivery, 201);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function show(Delivery $delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function edit(Delivery $delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Delivery $delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delivery $delivery)
    {
        //
    }

    public function updateLocation(Request $request, $deliveryId)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $delivery = Delivery::findOrFail($deliveryId);
        $delivery->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'last_updated_at' => now(),
        ]);

        // Publish to Redis
        Redis::publish("delivery-tracking.{$deliveryId}", json_encode([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'last_updated_at' => now(),
        ]));

        return response()->json(['message' => 'Location updated successfully']);
    }
}
