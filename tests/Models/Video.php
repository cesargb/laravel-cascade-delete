<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = 'tags';

    protected $fillable = ['name'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
