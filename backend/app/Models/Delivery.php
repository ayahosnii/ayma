<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'tracking_code',
        'status',
        'delivery_partner',
        'current_step',
        'timeline',
        'latitude',
        'longitude',
        'last_updated_at',
    ];
    protected $casts = [
        'timeline' => 'array', // For the JSON `timeline` field
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
