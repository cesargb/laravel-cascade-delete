<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class Photo extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['options'];

    protected $fillable = ['name'];

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }
}
