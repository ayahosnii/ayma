<?php

namespace App\Models\MongoModels;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProductsMongo extends Eloquent
{
    // Specify the MongoDB collection if different from default 'products_mongos'
    protected $collection = 'products'; // or whatever your collection is called

    // Optionally, you can specify the connection for MongoDB
    protected $connection = 'mongodb';

    // Define any fillable fields
    protected $fillable = ['name', 'sku', 'price', 'discount_price', 'description', 'stock', 'is_featured',
    'additional_attributes', 'category_id'];

    // Timestamps can be enabled or disabled
    public $timestamps = true;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
}
