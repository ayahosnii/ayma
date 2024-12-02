<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'slug', 'description', 'short_description', 'price',
        'discount_price', 'stock', 'low_stock_threshold', 'is_featured',
        'color_id', 'size_id', 'category_id',
    ];

    // Relationships
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_product')
            ->withPivot('purchase_price', 'purchase_date', 'quantity')
            ->withTimestamps();
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }
}
