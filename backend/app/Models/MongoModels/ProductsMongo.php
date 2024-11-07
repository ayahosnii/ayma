<?php

namespace App\Models\MongoModels;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProductsMongo extends Eloquent
{
    protected $collection = 'products';
    protected $connection = 'mongodb';
    protected $fillable = [
        'name',
        'sku',
        'price',
        'discount_price',
        'description',
        'stock',
        'is_featured',
        'additional_attributes',
        'category_id',
        'images' // Add images to the fillable array
    ];

    public $timestamps = true;

    public function category()
    {
    return $this->belongsTo(Category::class, 'category_id');
    }
}
