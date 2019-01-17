<?php

namespace Tests;

use Tests\Models\Tag;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\Option;
use Tests\Models\BadModel;
use Tests\Models\BadModel2;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getEnvironmentSetUp($this->app);

        $this->setUpDatabase($this->app);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('options', function (Blueprint $table) {
            $table->morphs('optionable');
            $table->string('name');
            $table->timestamps();
        });

        $photo = Photo::create(['name' => 'photo1']);

        $option = new Option();
        $option->name = 'option1';
        $photo->options()->save($option);

        $option = new Option();
        $option->name = 'option1b';
        $photo->options()->save($option);

        $photo = Photo::create(['name' => 'photo2']);

        $option = new Option();
        $option->name = 'option2';
        $photo->options()->save($option);

        $app['db']->connection()->getSchemaBuilder()->create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('taggables', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned();
            $table->morphs('taggable');
        });

        $tag1 = Tag::create(['name' => 'tag1']);
        $tag2 = Tag::create(['name' => 'tag2']);
        $tag3 = Tag::create(['name' => 'tag3']);
        $tag4 = Tag::create(['name' => 'tag4']);

        $video = Video::create(['name' => 'video1']);

        $video->tags()->attach([1, 2]);

        $video = Video::create(['name' => 'video2']);

        $video->tags()->attach([2, 3]);

        $app['db']->connection()->getSchemaBuilder()->create('bad_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        BadModel::create();

        $app['db']->connection()->getSchemaBuilder()->create('bad_model2s', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        BadModel2::create();
    }
}
