<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorySlide extends Model
{
    use HasFactory;

    protected $table = "stories_slides";
    protected $fillable = [
        'image',
        'context',
        'story_id',
    ];

    // Relationship with stories table (assuming belongs-to relationship)
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
