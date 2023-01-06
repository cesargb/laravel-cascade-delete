<?php

namespace Tests;

use Cesargb\Database\Support\Morph;
use Illuminate\Support\Facades\DB;
use Tests\Models\Image;
use Tests\Models\Option;
use Tests\Models\Photo;
use Tests\Models\Tag;
use Tests\Models\User;
use Tests\Models\Video;

class DeleteTest extends TestCase
{
    public function test_delete_morph_relations_from_event_model__morph_one()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        User::first()->delete();

        $this->assertEquals(1, Image::count());
    }

    public function test_delete_morph_relations_from_record_model__morph_one()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        (new Morph())->delete(User::first());

        $this->assertEquals(1, Image::count());
        $this->assertNull(User::first()->image);
    }

    public function test_delete_morph_relations_from_record_model__morph_many()
    {
        factory(Photo::class, 2)
            ->create()
            ->each(function ($photo) {
                $photo->options()->saveMany(factory(Option::class, 2)->make());
            });

        $this->assertEquals(4, Option::count());
        $this->assertEquals(2, Photo::first()->options()->count());

        (new Morph())->delete(Photo::first());

        $this->assertEquals(2, Option::count());
        $this->assertEquals(0, Photo::first()->options()->count());
    }

    public function test_delete_morph_relations_from_record_model__morph_to_many()
    {
        factory(Tag::class, 2)->create();

        factory(Video::class, 2)
            ->create()
            ->each(function ($video) {
                $video->tags()->attach(Tag::pluck('id'));
            });

        $this->assertEquals(2, Video::first()->tags()->count());
        $this->assertEquals(2, Video::skip(1)->first()->tags()->count());
        $this->assertEquals(4, DB::table('taggables')->count());

        (new Morph())->delete(Video::first());

        $this->assertEquals(0, Video::first()->tags()->count());
        $this->assertEquals(2, Video::skip(1)->first()->tags()->count());
        $this->assertEquals(2, DB::table('taggables')->count());
    }
}
