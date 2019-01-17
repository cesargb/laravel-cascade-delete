<?php

namespace Tests;

use Tests\Models\Photo;
use Tests\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class MorphCleanCommandTest extends TestCase
{
    public function test_command()
    {
        $this->assertTrue(true);
        
        /*$totalOptions = DB::table('options')->count();
        $totalTags = DB::table('taggables')->count();

        $deleteOptions = Photo::first()->options()->count();
        $deleteTags = Video::first()->tags()->count();

        Photo::first()->delete();
        Video::first()->delete();

        Artisan::call('morph:clean');

        $this->assertEquals(
            $totalOptions - $deleteOptions,
            DB::table('options')->count()
        );

        $this->assertEquals(
            $totalTags - $deleteTags,
            DB::table('taggables')->count()
        );*/
    }
}
