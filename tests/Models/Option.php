<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{    
    public function photos()
    {
        return $this->morphTo();
    }
}
