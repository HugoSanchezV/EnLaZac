<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledTask;
use Illuminate\Support\Facades\Schedule;
use Inertia\Inertia;

class ScheduledTaskController extends Controller
{
    public function index (){
        return Inertia::render('Admin/Settings/Background/Background' );

    }
    public function edit(Request $request)
    {
        switch($request->task){
            case 'device-stats':
                $task= ScheduledTask::where('task_name', 'device-stats')->first();
                break;
            case 'ping-routers':
                $task = ScheduledTask::where('task_name', 'ping-routers')->first();
                break;
            case 'check-contracts':
                $task = ScheduledTask::where('task_name', 'check-contracts')->first();
                break;
            default: 
            
        };
        return Inertia::render('Admin/Settings/Background/Edit',[
            'task' => $task,
            'title' => $request->task,
        ]);



    }
    public function toggleTask(Request $request)
    {

        //Agregar nombre de las tareas
        $validatedData = $request->validate(['user_id' => 'in: "ping-routers","device-stats","check-contracts"' ]);
        $task = ScheduledTask::where('task_name', $validatedData['name'])->first();

        if ($task) {
            $task->status = !$task->status;  // Alternar entre activado/desactivado
            $task->save();

            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        }else{

            ScheduledTask::create([
                'task_name' => $validatedData['name'],
                'status' => true,
            ]);
            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se encontró la tarea.');
    }
    public function updatePeriod(Request $request, $id)
    {
        $validatedData = $request->validate(['period' => 'requiered|in: "everyFiveMinutes","everyFifteenMinutes","everyThirtyMinutes","hourly", "daily"' ]);
        
        $schedule = ScheduledTask::findOrFail($id);

        $schedule->period = $validatedData['period'];

        $schedule->save();


    }
    public function status($id)
    {
        $task = ScheduledTask::find($id);
        if ($task) {
            return $task->status;
        }

        return redirect()->back()->with('error', 'Error al mostrar el estado de la automatización del ping');
    }
    
}
