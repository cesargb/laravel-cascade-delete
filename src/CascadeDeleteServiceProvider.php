<?php

namespace Cesargb\Database\Support;

use Cesargb\Database\Support\Commands\MorphCleanCommand;
use Illuminate\Support\ServiceProvider;

class CascadeDeleteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/morph.php' => config_path('morph.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MorphCleanCommand::class,
            ]);
        }
    }
}
