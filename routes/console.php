<?php

use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule de Ping-devices 

Schedule::call(function () {
    // Log::info('Tarea ejecutada');
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    // $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    // Log::info('dsad');
    if ($taskRouter->period == "everyFiveMinutes" && $taskRouter->status) {
        Artisan::call('app:ping-routers');
    }

    // if($taskContract->period == "everyFiveMinutes" && $taskContract->status)
    // {
    //    Artisan::call('app:check-contracts');  
    // }

    if ($taskDevice->period == "everyFiveMinutes" && $taskDevice->status) {
        Artisan::call('app:device-stats');
    }
})->everyFiveMinutes();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    // $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if ($taskRouter->period == "everyFifteenMinutes" && $taskRouter->status) {
        Artisan::call('app:ping-routers');
    }

    // if($taskContract->period == "everyFifteenMinutes" && $taskContract->status)
    // {
    //     Artisan::call('app:check-contracts');   
    // }

    if ($taskDevice->period == "everyFifteenMinutes" && $taskDevice->status) {
        Artisan::call('app:device-stats');
    }
})->everyFifteenMinutes();

Schedule::call(function () {
    // $task = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    // $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if ($taskRouter->period == "everyThirtyMinutes" && $taskRouter->status) {
        Artisan::call('app:ping-routers');
    }

    // if($taskContract->period == "everyThirtyMinutes" && $taskContract->status)
    // {
    //     Artisan::call('app:check-contracts');  
    // }

    if ($taskDevice->period == "everyThirtyMinutes" && $taskDevice->status) {
        Artisan::call('app:device-stats');
    }
})->everyThirtyMinutes();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    // $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if ($taskRouter->period == "hourly" && $taskRouter->status) {
        Artisan::call('app:ping-routers');
    }
    // if($taskContract->period == "hourly" && $taskContract->status)
    // {
    //     Artisan::call('app:check-contracts');  
    // }
    if ($taskDevice->period == "hourly" && $taskDevice->status) {
        Artisan::call('app:device-stats');
    }
})->hourly();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    // $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    $taskDevice = ScheduledTask::where('task_name', 'backups')->first();

    if ($taskDevice->period == "daily" && $taskDevice->status) {
        Artisan::call('app:create-backup');
    }

    if ($taskRouter->period == "daily" && $taskRouter->status) {
        Artisan::call('app:ping-routers');
    }
    // if($taskContract->period == "daily" && $taskContract->status)
    // {
    //     Artisan::call('app:check-contracts');
    // }
    if ($taskDevice->period == "daily" && $taskDevice->status) {
        Artisan::call('app:device-stats');
    }
})->daily();

Schedule::call(function () {
    $taskDevice = ScheduledTask::where('task_name', 'backups')->first();

    if ($taskDevice->period == "weekly" && $taskDevice->status) {
        Artisan::call('app:create-backup');
    }
})->weekly();

Schedule::call(function () {
    $taskDevice = ScheduledTask::where('task_name', 'backups')->first();

    if ($taskDevice->period == "monthly" && $taskDevice->status) {
        Artisan::call('app:create-backup');
    }
})->monthly();

Schedule::command('app:check-contracts')->daily();
Schedule::command('app:update-contract-date')->daily();
// Schedule::command('app:device-stats')->everyFiveMinutes();
