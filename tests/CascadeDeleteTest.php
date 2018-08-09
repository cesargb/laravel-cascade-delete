<?php

namespace Tests;

use Exception;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;
use Illuminate\Support\Facades\DB;

class CascadeDeleteTest extends TestCase
{
    public function test_it_can_delete_relations_with_morph_many()
    {
        $photo1 = Photo::with('options')->first();
        $photo2 = Photo::with('options')->skip(1)->first();

        $this->assertGreaterThan(0, count($photo1->options));
        $this->assertGreaterThan(0, count($photo2->options));

        Photo::first()->delete();

        $this->assertEquals(
            0,
            DB::table('options')->where([
                'optionable_type' => Photo::class,
                'optionable_id' => $photo1->id,
            ])->count()
        );

        $this->assertEquals(
            count($photo2->options),
            DB::table('options')->where([
                'optionable_type' => Photo::class,
                'optionable_id' => $photo2->id,
            ])->count()
        );
    }

    public function test_it_can_delete_relations_with_morph_to_many()
    {
        $video1 = Video::with('tags')->first();
        $video2 = Video::with('tags')->skip(1)->first();

        $this->assertGreaterThan(0, count($video1->tags));
        $this->assertGreaterThan(0, count($video2->tags));

        Video::first()->delete();

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

    /**
     * @expectedException          Exception
     * @expectedExceptionMessage   The class Tests\Models\BadModel not have the method bad_method
     */
    public function test_get_exception_if_method_not_exists()
    {
        BadModel::first()->delete();
    }

    /**
     * @expectedException          Exception
     * @expectedExceptionMessage   The relation bad_method must return an object of type Illuminate\Database\Eloquent\Relations\MorphMany or Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function test_get_exception_if_method_not_return_relation_morph()
    {
        BadModel2::first()->delete();
    }
}
