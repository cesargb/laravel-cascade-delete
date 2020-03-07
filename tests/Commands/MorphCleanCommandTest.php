<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\App;
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

        [$versionMayor, $versionMinor] = explode('.', App::version());

        if ($versionMayor == 5 && $versionMinor == 5) {
            $this->artisan('morph:clean', ['--dry-run' => true]);
        } else {
            $this->artisan('morph:clean')->assertExitCode(0);
        }

        $this->assertEquals(1, Image::count());
        $this->assertNotNull(User::where('id', 2)->first()->image);
    }

    public function testMorphCleanCommandWithDryRun()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        User::where('id', 1)->delete();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        list($versionMayor, $versionMinor) = explode('.', App::version());

        if ($versionMayor == 5 && $versionMinor == 5) {
            $this->artisan('morph:clean', ['--dry-run' => true]);
        } else {
            $this->artisan('morph:clean --dry-run')->assertExitCode(0);
        }

        $this->assertEquals(2, Image::count());
    }
}
