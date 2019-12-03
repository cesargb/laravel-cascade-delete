<?php

namespace Tests;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Support\Facades\DB;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;
use Tests\Models\Image;
use Tests\Models\Option;
use Tests\Models\Photo;
use Tests\Models\Tag;
use Tests\Models\User;
use Tests\Models\Video;

class CascadeDeleteResidualTest extends TestCase
{
    public function test_require_residual()
    {
        $totalImages = DB::table('images')->count();

        DB::table('users')->delete();

        $this->assertEquals(
            $totalImages,
            DB::table('images')->count()
        );

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
        $totalImages = DB::table('images')->count();

        User::query()->delete();

        $this->assertEquals(
            $totalImages,
            DB::table('images')->count()
        );

        $totalTags = DB::table('taggables')->count();

        Video::query()->delete();

        $this->assertEquals(
            $totalTags,
            DB::table('taggables')->count()
        );

        $totalOptions = DB::table('options')->count();

        Photo::query()->delete();

        $this->assertEquals(
            $totalOptions,
            DB::table('options')->count()
        );
    }

    public function test_it_can_delete_relations_with_morph_one()
    {
        $image1 = User::with('image')->first();
        $image2 = User::with('image')->skip(1)->first();

        $this->assertEquals(2, Image::count());
        $this->assertNotNull($image1->image);
        $this->assertNotNull($image2->image);

        User::where('id', '=', $image1->id)->delete();

        $this->assertEquals(2, Image::count());

        (new User)->deleteMorphResidual();

        $image2 = User::with('image')->find($image2->id);

        $this->assertEquals(1, Image::count());
        $this->assertNotNull($image2->image);
    }

    public function test_it_can_delete_relations_with_morph_many()
    {
        $photo1 = Photo::with('options')->first();
        $photo2 = Photo::with('options')->skip(1)->first();

        $this->assertEquals(3, Option::count());
        $this->assertGreaterThan(0, count($photo1->options));
        $this->assertGreaterThan(0, count($photo2->options));

        Photo::where('id', '=', $photo1->id)->delete();

        $this->assertEquals(3, Option::count());

        (new Photo)->deleteMorphResidual();

        $this->assertEquals(1, Option::count());

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

        $this->assertEquals(4, Tag::count());
        $this->assertGreaterThan(0, count($video1->tags));
        $this->assertGreaterThan(0, count($video2->tags));

        Video::where('id', '=', $video1->id)->delete();

        (new Video)->deleteMorphResidual();

        $this->assertEquals(4, Tag::count());

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

    public function test_it_can_delete_all_relations_if_dont_need_clean()
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

    public function test_it_can_delete_all_relations()
    {
        $totalImages = DB::table('images')->count();
        $totalOptions = DB::table('options')->count();
        $totalTags = DB::table('taggables')->count();

        $deleteImages = 1;
        $deleteOptions = Photo::first()->options()->count();
        $deleteTags = Video::first()->tags()->count();

        User::first()->delete();
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
            $totalImages - $deleteImages,
            DB::table('images')->count()
        );

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
