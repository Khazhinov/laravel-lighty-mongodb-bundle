<?php

declare(strict_types=1);

namespace Khazhinov\LaravelLightyMongoDBBundle;

use Illuminate\Support\ServiceProvider;
use Khazhinov\LaravelLightyMongoDBBundle\Console\Commands\Generator\ControllerMongoDBGenerator;
use Khazhinov\LaravelLightyMongoDBBundle\Console\Commands\Generator\ModelMongoDBGenerator;
use Khazhinov\LaravelLightyMongoDBBundle\Console\Commands\GeneratorMongoDB;

class LaravelLightyMongoDBBundleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPublishables();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/lighty-mongodb-bundle.php', 'lighty-mongodb-bundle');
        $this->registerCommands();
    }

    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__.'/../config/lighty-mongodb-bundle.php' => config_path('lighty-mongodb-bundle.php'),
        ], 'config');
    }

    protected function registerCommands(): void
    {
        $this->app->bind('command.lighty:generator-mongodb', GeneratorMongoDB::class);
        $this->app->bind('command.lighty:generate-model-mongodb', ModelMongoDBGenerator::class);
        $this->app->bind('command.lighty:generate-controller-mongodb', ControllerMongoDBGenerator::class);

        $this->commands([
            'command.lighty:generator-mongodb',
            'command.lighty:generate-model-mongodb',
            'command.lighty:generate-controller-mongodb',
        ]);
    }
}
