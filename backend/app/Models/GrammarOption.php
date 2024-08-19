<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrammarOption extends Model
{
    protected $fillable = ['option', 'is_correct'];

    public function question()
    {
        return $this->belongsTo(GrammarQuestion::class, 'grammar_question_id');
    }
}
