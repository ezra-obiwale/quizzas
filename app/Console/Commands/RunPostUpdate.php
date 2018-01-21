<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunPostUpdate extends Command
{
    use Traits\CommandsAndJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:on-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the script after composer is updated';

}
