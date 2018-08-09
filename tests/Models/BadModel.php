<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class BadModel extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = 'bad_method';
}
