<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Employee extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'employees';

    // Fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'hire_date',
        'job_title',
        'department',
        'salary',
    ];

    // Casting attributes to proper data types
    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2', // to store salary as a decimal with 2 digits after the decimal point
    ];

    // Relationship: An employee is associated with a user (one-to-one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
