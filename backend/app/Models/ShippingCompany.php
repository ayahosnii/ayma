<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'website',
        'description',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'country'
    ];
}
