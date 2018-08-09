<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;

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
