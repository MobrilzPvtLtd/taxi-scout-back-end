<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScheduledRunOnLive extends Command
{
    protected $signature = 'scheduledRunOnLive:run';
    protected $description = 'Run the scheduledRunOnLive command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Artisan::call('clear:otp');
        Artisan::call('order:expire');
        Artisan::call('clear:request');
        Artisan::call('notify:document:expires');
        Artisan::call('drivers:totrip');
        Artisan::call('assign_drivers:for_regular_rides');
        // Artisan::call('assign_drivers:for_schedule_rides');
        // Artisan::call('offline:drivers');
        $this->info('scheduledRunOnLive command executed!');
    }
}
