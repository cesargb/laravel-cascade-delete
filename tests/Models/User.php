<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property ?\Tests\Models\Image $image
 */
class User extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['image'];

    protected $fillable = ['name'];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
