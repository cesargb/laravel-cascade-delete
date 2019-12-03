<?php

namespace Tests\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Model;

class BadModel extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = 'bad_method';
}
