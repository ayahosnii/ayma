<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesMetricResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->product ? $this->product->name : null,  // Product name
            'units_sold' => $this->units_sold,
            'revenue' => $this->revenue,
            'stock' => $this->product ? $this->product->stock : null,  // Product stock
            'primary_image' => $this->product && $this->product->primaryImage ?
                $this->product->primaryImage->image_path : null,  // Image path
        ];
    }

}
