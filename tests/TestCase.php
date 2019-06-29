<?php

namespace Tests;

use Tests\Models\Tag;
use Tests\Models\User;
use Tests\Models\Image;
use Tests\Models\Photo;
use Tests\Models\Video;
use Tests\Models\Option;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Cesargb\Database\Support\CascadeDeleteServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->getEnvironmentSetUp($this->app);

        $this->setUpDatabase($this->app);

        $this->generateFactory();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CascadeDeleteServiceProvider::class,
        ];
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
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('images', function (Blueprint $table) {
            $table->morphs('imageable');
            $table->string('name');
            $table->timestamps();
        });

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

        $app['db']->connection()->getSchemaBuilder()->create('bad_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('bad_model2s', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    protected function generateFactory()
    {
        $user = User::create(['name' => 'user1']);

        $user->image()->save(new Image(['name' => 'image1']));

        $user = User::create(['name' => 'user2']);

        $user->image()->save(new Image(['name' => 'image2']));

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

        Tag::create(['name' => 'tag1']);
        Tag::create(['name' => 'tag2']);
        Tag::create(['name' => 'tag3']);
        Tag::create(['name' => 'tag4']);

        $video = Video::create(['name' => 'video1']);

        $video->tags()->attach([1, 2]);

        $video = Video::create(['name' => 'video2']);

        $video->tags()->attach([2, 3]);
    }
}
