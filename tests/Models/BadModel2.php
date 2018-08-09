<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;

class BadModel2 extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = 'bad_method';

    public function bad_method()
    {
    }
}
