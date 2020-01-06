<?php

namespace Tests;

use Tests\Models\Image;
use Tests\Models\User;

class CascadeDeleteTest extends TestCase
{
    public function testBootCascadeDelete()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        $this->assertEquals(2, Image::count());

        User::first()->delete();

        $this->assertEquals(1, Image::count());
    }

    public function testDeleteMorphResidual()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        $this->assertEquals(2, Image::count());

        User::where('id', 1)->delete();

        (new User)->deleteMorphResidual();

        $this->assertEquals(1, Image::count());
    }
}
