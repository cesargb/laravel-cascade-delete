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

        $this->expectExceptionCode(10);

        BadModel::create()->delete();

        // BadModel::first()->delete();
    }

    public function test_get_exception_if_method_not_return_relation_morph()
    {
        $this->expectException(LogicException::class);

        $this->expectExceptionCode(20);

        BadModel2::create()->delete();

        //BadModel2::first()->delete();
    }
}
