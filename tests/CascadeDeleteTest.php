<?php

namespace Tests;

use LogicException;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;
use Illuminate\Support\Facades\DB;

class CascadeDeleteTest extends TestCase
{
    /**
     * @expectedException          LogicException
     * @expectedExceptionMessage   The class Tests\Models\BadModel not have the method bad_method
     */
    public function test_get_exception_if_method_not_exists()
    {
        BadModel::first()->delete();
    }

    /**
     * @expectedException          LogicException
     * @expectedExceptionMessage   The relation bad_method must return an object of type Illuminate\Database\Eloquent\Relations\MorphMany or Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function test_get_exception_if_method_not_return_relation_morph()
    {
        BadModel2::first()->delete();
    }
}
