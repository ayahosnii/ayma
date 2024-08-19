<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorySound extends Model
{
    use HasFactory;

    protected $table = 'stories_sounds';
    protected $fillable = [
        'sound',
        'story_id',
    ];

    // Relationship with stories table (assuming belongs-to relationship)
    public function sound()
    {
        return $this->belongsTo(Story::class);
    }
}
