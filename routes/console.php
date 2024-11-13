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

    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if($taskRouter->period == "everyFiveMinutes")
    {
        if ($taskRouter->status) {
    
            Artisan::call('app:ping-routers');
        }
        
    }
    if($taskContract->period == "everyFiveMinutes")
    {
        if ($taskContract->status) {
    
            Artisan::call('app:check-contracts');
        }
        
    }
    if($taskDevice->period == "everyFiveMinutes")
    {
        if ($taskDevice->status) {
    
            Artisan::call('app:device-stats');
        }
        
    }
})->everyFiveMinutes();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if($taskRouter->period == "everyFifteenMinutes")
    {
        if ($taskRouter->status) {
    
            Artisan::call('app:ping-routers');
        }
        
    }
    if($taskContract->period == "everyFifteenMinutes")
    {
        if ($taskContract->status) {
    
            Artisan::call('app:check-contracts');
        }
        
    }
    if($taskDevice->period == "everyFifteenMinutes")
    {
        if ($taskDevice->status) {
    
            Artisan::call('app:device-stats');
        }
        
    }
  
})->everyFifteenMinutes();

Schedule::call(function () {
    $task = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if($taskRouter->period == "everyThirtyMinutes")
    {
        if ($taskRouter->status) {
    
            Artisan::call('app:ping-routers');
        }
        
    }
    if($taskContract->period == "everyThirtyMinutes")
    {
        if ($taskContract->status) {
    
            Artisan::call('app:check-contracts');
        }
        
    }
    if($taskDevice->period == "everyThirtyMinutes")
    {
        if ($taskDevice->status) {
    
            Artisan::call('app:device-stats');
        }
        
    }

})->everyThirtyMinutes();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if($taskRouter->period == "hourly")
    {
        if ($taskRouter->status) {
    
            Artisan::call('app:ping-routers');
        }
        
    }
    if($taskContract->period == "hourly")
    {
        if ($taskContract->status) {
    
            Artisan::call('app:check-contracts');
        }
        
    }
    if($taskDevice->period == "hourly")
    {
        if ($taskDevice->status) {
    
            Artisan::call('app:device-stats');
        }
        
    }
 
})->hourly();

Schedule::call(function () {
    $taskRouter = ScheduledTask::where('task_name', 'ping-routers')->first();
    $taskContract = ScheduledTask::where('task_name', 'check-contracts')->first();
    $taskDevice = ScheduledTask::where('task_name', 'device-stats')->first();
    if($taskRouter->period == "daily")
    {
        if ($taskRouter->status) {
    
            Artisan::call('app:ping-routers');
        }
        
    }
    if($taskContract->period == "daily")
    {
        if ($taskContract->status) {
    
            Artisan::call('app:check-contracts');
        }
        
    }
    if($taskDevice->period == "daily")
    {
        if ($taskDevice->status) {
    
            Artisan::call('app:device-stats');
        }
        
    }

})->daily();

Schedule::command('app:check-contracts')->daily();
Schedule::command('app:device-stats')->everyFiveMinutes();
