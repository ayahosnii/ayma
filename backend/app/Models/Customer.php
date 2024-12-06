<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'customers';

    // Fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'loyalty_points',
        'preferred_payment_method',
        'last_purchase_date',
        'customer_preferences',
    ];

    // Casting attributes to proper data types
    protected $casts = [
        'customer_preferences' => 'array', // cast JSON to an array
        'last_purchase_date' => 'datetime',
    ];

    // Relationship: A customer is associated with a user (one-to-one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

