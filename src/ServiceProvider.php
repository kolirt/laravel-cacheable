<?php

namespace Kolirt\Cacheable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    protected array $commands = [
        Commands\InstallCommand::class,
        Commands\PublishConfigConsoleCommand::class,
    ];

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cacheable.php', 'cacheable');

        $this->publishFiles();
    }

    public function register(): void
    {
        $this->commands($this->commands);
    }

    private function publishFiles(): void
    {
        $this->publishes([
            __DIR__ . '/../config/cacheable.php' => config_path('cacheable.php')
        ], 'config');
    }

}
