<?php

use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule de Ping-devices 
Schedule::call(function () {
    $task = ScheduledTask::where('task_name', 'ping-routers')->first();

    if ($task->enabled) {

        Artisan::call('app:ping-routers');
    }
    // $this->info('dsad');*/
})->everyFifteenMinutes();

Schedule::command('app:check-contracts')->daily();
Schedule::command('app:device-stats')->everyFiveMinutes();
//Schedule::command('app:ping-devices')->everyThirtyMinutes();