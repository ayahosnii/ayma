<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['listening_id', 'question', 'answer'];

    public function listening()
    {
        return $this->belongsTo(Listening::class);
    }
}
