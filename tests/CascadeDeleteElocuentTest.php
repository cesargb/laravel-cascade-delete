<?php

namespace Tests;

use Tests\Models\Tag;
use Tests\Models\User;
use Tests\Models\Image;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\Option;
use Illuminate\Support\Facades\DB;

class CascadeDeleteElocuentTest extends TestCase
{
    public function test_it_can_delete_relations_with_morph_one()
    {
        $image1 = User::with('image')->first();
        $image2 = User::with('image')->skip(1)->first();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull($image1->image);
        $this->assertNotNull($image2->image);

        User::first()->delete();

        $image2 = User::with('image')->find($image2->id);

        $this->assertEquals(1, Image::count());
        $this->assertNotNull($image2->image);
    }

    public function test_it_can_delete_relations_with_morph_many()
    {
        $photo1 = Photo::with('options')->first();
        $photo2 = Photo::with('options')->skip(1)->first();

        $this->assertEquals(3, Option::count());
        $this->assertEquals(2, count($photo1->options));
        $this->assertEquals(1, count($photo2->options));

        Photo::first()->delete();

        $photo2 = Photo::with('options')->find($photo2->id);

        $this->assertEquals(1, Option::count());
        $this->assertEquals(1, count($photo2->options));
    }

    public function test_it_can_delete_relations_with_morph_to_many()
    {
        $video1 = Video::with('tags')->first();
        $video2 = Video::with('tags')->skip(1)->first();

        $this->assertEquals(4, Tag::count());
        $this->assertEquals(2, count($video1->tags));
        $this->assertEquals(2, count($video2->tags));

        Video::first()->delete();

        $this->assertEquals(4, Tag::count());

        $this->assertEquals(
            0,
            DB::table('taggables')->where([
                'taggable_type' => Video::class,
                'taggable_id' => $video1->id,
            ])->count()
        );

        $this->assertEquals(
            count($video2->tags),
            DB::table('taggables')->where([
                'taggable_type' => Video::class,
                'taggable_id' => $video2->id,
            ])->count()
        );
    }
}
