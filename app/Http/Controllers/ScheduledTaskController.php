<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduledTask\StoreScheduledTaskRequest;
use App\Http\Requests\ScheduledTask\UpdateScheduledTaskRequest;
use Illuminate\Http\Request;
use App\Models\ScheduledTask;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ScheduledTaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Background/Background');
    }
    public function edit(Request $request)
    {
        try{

            switch ($request->task) {
                case 'device-stats':
                    $task = ScheduledTask::where('task_name', 'device-stats')->first();
                    break;
                case 'ping-routers':
                    $task = ScheduledTask::where('task_name', 'ping-routers')->first();
                    break;
                case 'check-contracts':
                    $task = ScheduledTask::where('task_name', 'check-contracts')->first();
                    break;
                case 'backups':
                    $task = ScheduledTask::where('task_name', 'backups')->first();
                    break;
                default:
            };
            return Inertia::render('Admin/Settings/Background/Edit', [
                'task' => $task,
                'title' => $request->task,
            ]);
        }catch(Exception $e){
            return Redirect::back()->with('error', 'Error al cargar las opciones');

        }
    }
    public function store(StoreScheduledTaskRequest $request)
    {
        try{// dd($request);
            $validatedData = $request->validated();
            ScheduledTask::create([
                'task_name' => $validatedData['name'],
                'period' => $validatedData['period'],
                'status' => $validatedData['status'],
            ]);
            return redirect()->route('settings.background')->with('success', 'La Tarea se ha sido Actualizado Con Éxito');
        }catch(Exception $e){
            return redirect()->route('settings.background')->with('error', 'Hubo un error al actualizar el registro');

        }
    }
    public function update(UpdateScheduledTaskRequest $request, $id)
    {
        try{

            $task = ScheduledTask::findOrFail($id);
            $validatedData = $request->validated();
    
            $task->period = $validatedData['period'];
            $task->status = $validatedData['status'];
            $task->save();
    
            return redirect()->route('settings.background')->with('success', 'La Tarea se ha sido Actualizado Con Éxito');

        }catch(Exception $e){
            return redirect()->route('settings.background')->with('error', 'Hubo un error al actualizar el registro');

        }
    }
    public function toggleTask(Request $request)
    {

        try{
           // dd($request['task']);
            $resultado['task'] = $request->task;

            Validator::make($resultado, [
                'task' => 'in: "ping-routers","device-stats","check-contracts", "backups | string',
            ])->validate();

            //$validatedData = $resultado->validate(['task' => 'in: "ping-routers","device-stats","check-contracts", "backups']);
          //  dd($resultado );
            $task = ScheduledTask::where('task_name', $resultado['task'])->first();

            if ($task) {
                $task->status = !$task->status;  // Alternar entre activado/desactivado
                
                $task->save();

                return redirect()->back()->with('success', 'Tarea actualizada correctamente.');
            } else {

                ScheduledTask::create([
                    'task_name' => $resultado['task'],
                    'period' => 'everyFiveMinutes',
                    'status' => true,
                ]);
                return redirect()->back()->with('success', 'Tarea actualizada correctamente.');
            }

            return redirect()->back()->with('error', 'No se encontró la tarea.');
        }catch(Exception $e)
        {
            return redirect()->back()->with('error', 'No se encontró la tarea.');
        }
        //Agregar nombre de las tareas
        
    }
    public function updatePeriod(Request $request, $id)
    {
        try{
            $validatedData = $request->validate(['period' => 'requiered|in: "everyFiveMinutes","everyFifteenMinutes","everyThirtyMinutes","hourly", "daily", "weekly", "monthly"']);

            $schedule = ScheduledTask::findOrFail($id);

            $schedule->period = $validatedData['period'];

            $schedule->save();
        }catch(Exception $e){

            Log::error("ERROR AL ACTUALIZAR EL PERIODO");
        }
        
    }
    public function status($name)
    {
        try{
            $task = ScheduledTask::where('task_name', $name)->first();
            if ($task) {
                return $task->status;
            }

            return redirect()->back()->with('error', 'Error al mostrar el estado de la automatización del ping');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Error al mostrar el estado de la automatización del ping');

        }
        
    }
}
