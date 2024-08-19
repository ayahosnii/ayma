<?php

namespace App\Models;

use App\Models\GrammarOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrammarQuestion extends Model
{
    protected $fillable = ['text', 'grammar_game_id'];

    public function options()
    {
        return $this->hasMany(GrammarOption::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(GrammarGame::class, 'grammar_game_id');
    }
}

