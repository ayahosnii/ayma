<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryShippingCompany extends Model
{
    use HasFactory;

    // Define table name if different from the default plural form
    protected $table = 'delivery_shipping_company';

    // Define fillable properties if needed
    protected $fillable = [
        'user_id',
        'shipping_company_id',
    ];

    /**
     * Define the relationship between the DeliveryShippingCompany and User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship between the DeliveryShippingCompany and ShippingCompany.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }
}
