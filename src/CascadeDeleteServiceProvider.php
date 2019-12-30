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
        if ($this->app->runningInConsole()) {
            $this->commands([
                MorphCleanCommand::class,
            ]);
        }
    }
}
