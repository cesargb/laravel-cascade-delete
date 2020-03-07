<?php

namespace Tests\Events;

use Cesargb\Database\Support\Events\RelationMorphFromModelWasCleaned;
use Cesargb\Database\Support\Morph;
use Illuminate\Support\Facades\Event;
use Tests\Models\Image;
use Tests\Models\User;
use Tests\TestCase;

class EventTest extends TestCase
{
    public function testRelationMorphFromModelWasCleaned()
    {
        Event::fake();

        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        User::where('id', 1)->delete();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        (new Morph)->cleanResidualByModel(new User());

        Event::assertDispatched(RelationMorphFromModelWasCleaned::class, 1);
        Event::assertDispatched(
            RelationMorphFromModelWasCleaned::class,
            function (RelationMorphFromModelWasCleaned $event) {
                return $event->numDeleted === 1;
            }
        );
    }
}
