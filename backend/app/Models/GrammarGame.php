<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrammarGame extends Model
{
    protected $fillable = ['title', 'level_id'];

    public function question()
    {
        return $this->hasMany(GrammarQuestion::class, 'grammar_question_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
