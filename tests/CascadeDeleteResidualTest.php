<?php

namespace Tests;

use LogicException;
use Cesargb\Database\Support\CascadeDelete;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;
use Illuminate\Support\Facades\DB;

class CascadeDeleteResidualTest extends TestCase
{
    public function test_require_residual()
    {
        $totalTags = DB::table('taggables')->count();

        DB::table('videos')->delete();

        $this->assertEquals(
            $totalTags,
            DB::table('taggables')->count()
        );

        $totalOptions = DB::table('options')->count();

        DB::table('photos')->delete();

        $this->assertEquals(
            $totalOptions,
            DB::table('options')->count()
        );
    }

    public function test_require_residual_with_models()
    {
        $totalTags = DB::table('taggables')->count();

        Video::query()->delete();

        $this->assertEquals(
            $totalTags,
            DB::table('taggables')->count()
        );

        $totalOptions = DB::table('options')->count();

        Photo::query()->delete();;

        $this->assertEquals(
            $totalOptions,
            DB::table('options')->count()
        );
    }

    public function test_it_can_delete_relations_with_morph_many()
    {
        $photo1 = Photo::with('options')->first();
        $photo2 = Photo::with('options')->skip(1)->first();

        $this->assertGreaterThan(0, count($photo1->options));
        $this->assertGreaterThan(0, count($photo2->options));

        Photo::where('id', '=', $photo1->id)->delete();

        (new Photo)->deleteMorphResidual();

        $this->assertEquals(
            0,
            DB::table('options')->where([
                'optionable_type'   => Photo::class,
                'optionable_id'     => $photo1->id,
            ])->count()
        );

        $this->assertEquals(
            count($photo2->options),
            DB::table('options')->where([
                'optionable_type'   => Photo::class,
                'optionable_id'     => $photo2->id,
            ])->count()
        );

        $this->assertEquals(
            count($photo2->options),
            DB::table('options')->count()
        );

    }

    public function test_it_can_delete_relations_with_morph_to_many()
    {
        $totalTags = DB::table('taggables')->count();

        $video1 = Video::with('tags')->first();
        $video2 = Video::with('tags')->skip(1)->first();

        $this->assertGreaterThan(0, count($video1->tags));
        $this->assertGreaterThan(0, count($video2->tags));

        Video::where('id', '=', $video1->id)->delete();

        (new Video)->deleteMorphResidual();

        $this->assertEquals(
            0,
            DB::table('taggables')->where([
                'taggable_type' => Video::class,
                'taggable_id'   => $video1->id,
            ])->count()
        );

        $this->assertEquals(
            count($video2->tags),
            DB::table('taggables')->where([
                'taggable_type' => Video::class,
                'taggable_id'   => $video2->id,
            ])->count()
        );

        $this->assertEquals(
            $totalTags - count($video1->tags),
            DB::table('taggables')->count()
        );
    }

    public function test_it_can_delete_al_relations_if_dont_need_clean()
    {
        $totalOptions = DB::table('options')->count();
        $totalTags = DB::table('taggables')->count();

        foreach (get_declared_classes() as $class) {
            if (array_key_exists(CascadeDelete::class, class_uses($class))) {
                if ($class != BadModel::class && $class != BadModel2::class) {
                    (new $class)->deleteMorphResidual();
                }
            }
        }

        $this->assertEquals($totalOptions, DB::table('options')->count());
        $this->assertEquals($totalTags, DB::table('tags')->count());
    }

    public function test_it_can_delete_al_relations()
    {
        $totalOptions = DB::table('options')->count();
        $totalTags = DB::table('taggables')->count();

        $deleteOptions = Photo::first()->options()->count();
        $deleteTags = Video::first()->tags()->count();

        Photo::first()->delete();
        Video::first()->delete();

        foreach (get_declared_classes() as $class) {
            if (array_key_exists(CascadeDelete::class, class_uses($class))) {
                if ($class != BadModel::class && $class != BadModel2::class) {
                    (new $class)->deleteMorphResidual();
                }
            }
        }

        $this->assertEquals(
            $totalOptions - $deleteOptions,
            DB::table('options')->count()
        );

        $this->assertEquals(
            $totalTags - $deleteTags,
            DB::table('taggables')->count()
        );
    }
}
