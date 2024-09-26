<?php

use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
        
//Schedule de Ping-devices 
Schedule::call(function(){
    $task = ScheduledTask::where('task_name', 'ping-routers')->first();

        if ($task && $task->enabled) {
            Artisan::command('app:ping-routers');
        }
})->everyThirtyMinutes();

Schedule::command('app:check-contracts')->daily();
Schedule::command('app:ping-devices')->everyThirtyMinutes();