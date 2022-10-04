<?php

namespace KlinikPintar;

use Illuminate\Support\ServiceProvider;
use KlinikPintar\Commands\CreateRepositoryCommand;

class KlinikPintarServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRepositoryCommand::class,
            ]);
        }
    }
}
