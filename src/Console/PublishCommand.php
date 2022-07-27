<?php

namespace LaravelCms\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish assets of the CMS';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag'   => 'assets',
            '--force' => true,
        ]);
    }
}
