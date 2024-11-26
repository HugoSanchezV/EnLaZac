<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduledTask\StoreScheduledTaskRequest;
use App\Http\Requests\ScheduledTask\UpdateScheduledTaskRequest;
use Illuminate\Http\Request;
use App\Models\ScheduledTask;
use Illuminate\Support\Facades\Schedule;
use Inertia\Inertia;

class ScheduledTaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Background/Background');
    }
    public function edit(Request $request)
    {
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
    }
    public function store(StoreScheduledTaskRequest $request)
    {
        // dd($request);
        $validatedData = $request->validated();
        ScheduledTask::create([
            'task_name' => $validatedData['name'],
            'period' => $validatedData['period'],
            'status' => $validatedData['status'],
        ]);
        return redirect()->route('settings.background')->with('success', 'La Tarea se ha sido Actualizado Con Éxito');
    }
    public function update(UpdateScheduledTaskRequest $request, $id)
    {
        $task = ScheduledTask::findOrFail($id);
        $validatedData = $request->validated();

        $task->period = $validatedData['period'];
        $task->status = $validatedData['status'];
        $task->save();

        return redirect()->route('settings.background')->with('success', 'La Tarea se ha sido Actualizado Con Éxito');
    }
    public function toggleTask(Request $request)
    {

        //Agregar nombre de las tareas
        $validatedData = $request->validate(['task_name' => 'in: "ping-routers","device-stats","check-contracts", "backups']);
        $task = ScheduledTask::where('task_name', $validatedData['name'])->first();

        if ($task) {
            $task->status = !$task->status;  // Alternar entre activado/desactivado
            $task->save();

            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        } else {

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
        $validatedData = $request->validate(['period' => 'requiered|in: "everyFiveMinutes","everyFifteenMinutes","everyThirtyMinutes","hourly", "daily", "weekly", "monthly"']);

        $schedule = ScheduledTask::findOrFail($id);

        $schedule->period = $validatedData['period'];

        $schedule->save();
    }
    public function status($name)
    {
        $task = ScheduledTask::where('task_name', $name)->first();
        if ($task) {
            return $task->status;
        }

        return redirect()->back()->with('error', 'Error al mostrar el estado de la automatización del ping');
    }
}
