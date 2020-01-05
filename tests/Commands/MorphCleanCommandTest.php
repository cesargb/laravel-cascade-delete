<?php

use Tests\Models\Image;
use Tests\Models\User;
use Tests\TestCase;

class MorphCleanCommandTest extends TestCase
{
    public function testMorphCleanCommand()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        User::where('id', 1)->delete();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        $this->artisan('morph:clean')->assertExitCode(0);

        $this->assertEquals(1, Image::count());
        $this->assertNotNull(User::where('id', 2)->first()->image);
    }
}