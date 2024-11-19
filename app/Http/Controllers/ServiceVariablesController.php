<?php

namespace App\Http\Controllers;

use App\Models\CutOffDay;
use App\Models\ExemptionPeriod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ServiceVariablesController extends Controller
{
    public function edit()
    {
        try{
            $cutOffDay = CutOffDay::first()->day ?? null;
            $exemptionPeriod = ExemptionPeriod::first();
            ///dd("Dad");
           // dd($cutOffDay);
            return Inertia::render('Admin/Settings/ServiceVariable/Edit', [
                'cutoffday' => $cutOffDay,
                'exemptionPeriod' =>$exemptionPeriod,
                'success' => session('success') ?? null,
                'error' => session('error') ?? null,
    
            ]);
        }catch(Exception $e){
            return Redirect::route('settings')->
            with('error', 'Hubo un error al mostrar las variables de servicio');
        }   
       

    }
    public function updateCutOffDay(Request $request){
        try{
            $validated = $request->validate([
                'day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
            ]);
            CutOffDay::updateOrCreate([], ['day' => $validated['day']]);

            return Redirect::route('settings.service.variable')->
            with('success', 'El Dia de Corte fue Actualizado Con Éxito');
        }catch(Exception $e){
            return Redirect::route('settings.service.variable')->
            with('error', 'Hubo un error al actualizar el dia de corte');
        }
    }
    public function updateExemptionPeriod(Request $request){
        try{
            //dd($request);
            $validated = $request->validate([
                'start_day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
                'end_day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
                'month_next' => 'required|integer', // Validar que sea un día válido
                'month_after_next' => 'required|integer', // Validar que sea un día válido
            ]);
            ExemptionPeriod::updateOrCreate(
                [], // Sin condición para búsqueda porque solo habrá un registro
                [
                    'start_day' => $validated['start_day'],
                    'end_day' => $validated['end_day'],
                    'month_next' => $validated['month_next'],
                    'month_after_next' => $validated['month_after_next'],
                ]
            );

            return Redirect::route('settings.service.variable')->
            with('success', 'Los Periodos de Excepcion fueron Actualizados Con Éxito');
        }catch(Exception $e){
            //dd($e);
            return Redirect::route('settings.service.variable')->
            with('error', 'Hubo un error al actualizar los periodos');
        }
 
    }
    public function getCutOffDay()
    {
        try{return CutOffDay::first()->day ?? null;}
        catch(Exception $e){
            Log::info($e);
            return null;
        }
    }

    public function getExemptionPeriods()
    { 
        try{return ExemptionPeriod::first();}
        catch(Exception $e){
            Log::info($e);
            return null;
        }     
    }


}
