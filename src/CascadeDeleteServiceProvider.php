<?php

namespace Cesargb\Database\Support;

use Illuminate\Support\ServiceProvider;
use Cesargb\Database\Support\MorphCleanCommand;

class CascadeDeleteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MorphCleanCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
