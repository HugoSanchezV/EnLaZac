<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledTask;
use Illuminate\Support\Facades\Schedule;
class ScheduledTaskController extends Controller
{
    public function toggleTask($name)
    {
        $task = ScheduledTask::where('task_name',$name)->first();

        if ($task) {
            $task->enabled = !$task->enabled;  // Alternar entre activado/desactivado
            $task->save();

            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        }else{

            ScheduledTask::create([
                'task_name' => $name,
                'enabled' => true,
            ]);
            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se encontró la tarea.');
    }
    public function status($id)
    {
        $task = ScheduledTask::find($id);
        if ($task) {
            return $task->enabled;
        }

        return redirect()->back()->with('error', 'Error al mostrar el estado de la automatización del ping');
    }
    
}
