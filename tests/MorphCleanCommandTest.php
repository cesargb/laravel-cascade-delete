<?php

namespace Tests;

use Tests\Models\User;
use Tests\Models\Image;
use Tests\Models\Photo;
use Tests\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class MorphCleanCommandTest extends TestCase
{
    public function test_command_delete_morph_one()
    {
        $totalImages = Image::count();

        $deleteImages = 1;

        User::where('id', User::first()->id)->delete();

        $this->assertEquals(
            $totalImages,
            Image::count()
        );

        Artisan::call('morph:clean');

        $this->assertEquals(
            $totalImages - $deleteImages,
            Image::count()
        );
    }

    public function test_command_delete_morph_many()
    {
        $totalOptions = DB::table('options')->count();

        $deleteOptions = Photo::first()->options()->count();

        Photo::where('id', Photo::first()->id)->delete();

        $this->assertEquals(
            $totalOptions,
            DB::table('options')->count()
        );

        Artisan::call('morph:clean');

        $this->assertGreaterThan(0, $deleteOptions);

        $this->assertEquals(
            $totalOptions - $deleteOptions,
            DB::table('options')->count()
        );
    }

    public function test_command_delete_morph_to_many()
    {
        $totalTags = DB::table('taggables')->count();

        $deleteTags = Video::first()->tags()->count();

        Video::where('id', Video::first()->id)->delete();

        $this->assertEquals(
            $totalTags,
            DB::table('taggables')->count()
        );

        Artisan::call('morph:clean');

        $this->assertGreaterThan(0, $deleteTags);

        $this->assertEquals(
            $totalTags - $deleteTags,
            DB::table('taggables')->count()
        );
    }
}
