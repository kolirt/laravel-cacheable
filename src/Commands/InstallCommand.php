<?php

namespace Kolirt\Cacheable\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{

    protected $signature = 'cacheable:install';

    protected $description = 'Install cacheable package';

    public function handle(): void
    {
        $this->call(PublishConfigConsoleCommand::class);
    }

}
