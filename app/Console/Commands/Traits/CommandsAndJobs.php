<?php

namespace App\Console\Commands\Traits;

use Carbon\Carbon;

trait CommandsAndJobs {

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // run commands
        echo "Running commands ...\n";
        foreach ($this->commands() as $command => $params) {
            $this->call($command, $params);
        }
        // dispatch jobs
        echo "Dispatching jobs ...\n";
        foreach ($this->jobs() as $class_name => $delay) {
            $dispatched = dispatch(new $class_name);
            if ($delay) $dispatched->delay(Carbon::now()->addMinutes($delay));
        }
    }

    protected function commands() {
        return [
            // For example:
            // 'email:send' => [
            //     'user' => 1,
            //     '--queue' => 'default'
            // ]
        ];
    }

    protected function jobs() {
        return [
            // For example:
            // \App\Jobs\SendEmail::class => 10 // delay job for 10 minutes
        ];
    }
}