<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    use HasFactory;

    protected $table = 'supplier_product';

    protected $fillable = [
        'supplier_id',
        'product_id',
        'purchase_price',
        'purchase_date',
        'quantity',
    ];

    /**
     * Relationship: Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship: Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
