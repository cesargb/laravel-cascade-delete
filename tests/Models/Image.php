<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['name'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
