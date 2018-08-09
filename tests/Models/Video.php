<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

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
