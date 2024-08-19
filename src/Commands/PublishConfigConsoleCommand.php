<?php

namespace Kolirt\Cacheable\Commands;

use Illuminate\Console\Command;

class PublishConfigConsoleCommand extends Command
{

    protected $signature = 'cacheable:publish-config';

    protected $description = 'Publish the config file';

    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--provider' => 'Kolirt\\Cacheable\\ServiceProvider',
            '--tag' => 'config'
        ]);
    }

}
