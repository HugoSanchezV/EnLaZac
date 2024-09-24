<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleTask;
use Illuminate\Support\Facades\Schedule;
class ScheduleTaskController extends Controller
{
    public function toggleTask(Request $request, $id)
    {
        $task = ScheduleTask::find($id);
        if ($task) {
            $task->enabled = !$task->enabled;  // Alternar entre activado/desactivado
            $task->save();

            return redirect()->back()->with('status', 'Tarea actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se encontró la tarea.');
    }
    public function singlePing(Request $request, $id)
    {
        
    }
    
}
