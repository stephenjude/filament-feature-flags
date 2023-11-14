<?php

namespace Stephenjude\FilamentFeatureFlag\Commands;

use Illuminate\Console\Command;

class FeaturePluginCommand extends Command
{
    public $signature = 'filament-feature-flags';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
