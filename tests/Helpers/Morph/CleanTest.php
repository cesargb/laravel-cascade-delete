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

class CleanTest extends TestCase
{
    public function testCleanResidualMorphRelationsFromModelMorphOne()
    {
        factory(User::class, 2)
            ->create()
            ->each(function ($user) {
                $user->image()->save(factory(Image::class)->make());
            });

        User::where('id', 1)->delete();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull(User::first()->image);

        $numRowsDeleted = Morph::cleanResidualMorphRelationsFromModel(new User());

        $this->assertEquals(1, $numRowsDeleted);
        $this->assertEquals(1, Image::count());
        $this->assertNotNull(User::where('id', 2)->first()->image);
    }

    public function testCleanResidualMorphRelationsFromModelMorphMany()
    {
        factory(Photo::class, 2)
            ->create()
            ->each(function ($photo) {
                $photo->options()->saveMany(factory(Option::class, 2)->make());
            });

        Photo::where('id', 1)->delete();

        $this->assertEquals(4, Option::count());
        $this->assertEquals(2, Photo::first()->options()->count());

        $numRowsDeleted = Morph::cleanResidualMorphRelationsFromModel(new Photo());

        $this->assertEquals(2, $numRowsDeleted);
        $this->assertEquals(2, Option::count());
        $this->assertNotNull(2, Photo::where('id', 2)->first()->options()->count());
    }

    public function testCleanResidualMorphRelationsFromModelMorphToMany()
    {
        factory(Tag::class, 2)->create();

        factory(Video::class, 2)
            ->create()
            ->each(function ($video) {
                $video->tags()->attach(Tag::pluck('id'));
            });

        Video::where('id', 1)->delete();

        $this->assertEquals(4, DB::table('taggables')->count());

        $numRowsDeleted = Morph::cleanResidualMorphRelationsFromModel(new Video());

        $this->assertEquals(2, $numRowsDeleted);
        $this->assertEquals(2, DB::table('taggables')->count());
        $this->assertEquals(2, Video::where('id', 2)->first()->tags()->count());
    }

    public function testCleanResidualMorphRelations()
    {
        factory(Tag::class, 2)->create();

        factory(Video::class, 2)
            ->create()
            ->each(function ($video) {
                $video->tags()->attach(Tag::pluck('id'));
            });

        Video::where('id', 1)->delete();

        $this->assertEquals(4, DB::table('taggables')->count());

        Morph::cleanResidualMorphRelations();

        $this->assertEquals(2, DB::table('taggables')->count());
        $this->assertEquals(2, Video::where('id', 2)->first()->tags()->count());
    }

    public function testCleanResidualMorphRelationsWhenMorphDontExist()
    {
        $tag = factory(Tag::class)->create();

        DB::table('taggables')->insert([
            'taggable_type' => 'ModelDontExists',
            'taggable_id' => 1,
            'tag_id' => $tag->id,
        ]);

        $this->assertEquals(1, DB::table('taggables')->count());

        Morph::cleanResidualMorphRelations();

        $this->assertEquals(0, DB::table('taggables')->count());
    }
}
