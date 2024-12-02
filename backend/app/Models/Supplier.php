<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'name',
        'contact_person',
        'contact_info',
        'phone',
        'email',
        'address',
        'country',
        'city',
        'postal_code',
        'website',
        'tax_id',
        'status',
        'notes',
        'supplier_type',
    ];

    // Define the relationship to products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'supplier_product')
            ->withPivot('purchase_price', 'purchase_date', 'quantity')
            ->withTimestamps();
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }
}
