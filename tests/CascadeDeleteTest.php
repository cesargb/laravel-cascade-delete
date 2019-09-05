<?php

namespace Tests;

use LogicException;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;

class CascadeDeleteTest extends TestCase
{
    public function test_get_exception_if_method_not_exists()
    {
        $this->expectException(LogicException::class);

        $this->expectExceptionMessage('The class Tests\Models\BadModel not have the method bad_method');

        BadModel::create();

        BadModel::first()->delete();
    }

    public function test_get_exception_if_method_not_return_relation_morph()
    {
        $this->expectException(LogicException::class);

        $this->expectExceptionMessage('The relation bad_method must return an object of type Illuminate\Database\Eloquent\Relations\MorphMany or Illuminate\Database\Eloquent\Relations\MorphToMany');

        BadModel2::create();

        BadModel2::first()->delete();
    }
}
