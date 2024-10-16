<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model; // Correct MongoDB Model

class Rating extends Model
{
    protected $connection = 'mongodb'; // MongoDB connection

    protected $fillable = ['user_id', 'product_id', 'rating', 'comment'];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
