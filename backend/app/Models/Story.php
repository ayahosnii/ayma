<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'title','level_id'
    ];

    // Relationship with stories_slides table (assuming one-to-many relationship)
    public function slides()
    {
        return $this->hasMany(StorySlide::class);
    }

    public function sounds()
    {
        return $this->hasMany(StorySound::class);
    }

    // Relationship with Level
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
