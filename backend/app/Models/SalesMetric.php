<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SalesMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'units_sold',
        'revenue',
    ];

    /**
     * Get the product associated with the sales metric.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
