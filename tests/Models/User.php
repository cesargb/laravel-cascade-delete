<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Tests\Models\Image $image
 */
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
