<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class User extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['image'];

    protected $fillable = ['name'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
