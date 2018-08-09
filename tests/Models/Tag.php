<?php

namespace Tests\Models;

use Tests\Models\Video;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [ 'name' ];

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}
