<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AnywhereBarttars;

class RunPostInstall extends Command
{

    use Traits\CommandsAndJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:on-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the script after the composer is installed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function commands() {
        return [
            'migrate' => [
                '--force' => true,
                // '--seed' => true
            ],
            'migrate' => [
                '--path' => 'database/migrations/v1',
                '--force' => true,
                // '--seed' => true
            ],
            // 'emails:send' => [
            //     '--custom' => true,
            //     '--subject' => 'With Love from Barttar',
            //     '--from' => 'adminbarttar@repools.com',
            //     // '--to' => 'ezra@repools.com,dantelex2@gmail.com'
            // ]
        ];
    }

    protected function jobs() {
        return [
            // \App\Jobs\AnywhereBarttars::class => 20,
            \App\Jobs\GiveAllUserBarttarRole::class => 0
        ];
    }
}
