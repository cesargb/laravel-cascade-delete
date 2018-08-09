<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class BadModel2 extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = 'bad_method';

    public function bad_method()
    {
        return null;
    }
}
