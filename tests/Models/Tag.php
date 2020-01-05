<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    protected $cascadeDeleteMorph = 'videos';

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}
