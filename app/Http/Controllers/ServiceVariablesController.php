<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\CutOffDay;
use App\Models\EquipmentChargeDay;
use App\Models\ExemptionPeriod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use InvalidArgumentException;

class ServiceVariablesController extends Controller
{
    public function edit()
    {
        try{
            $cutOffDay = CutOffDay::first()->day ?? null;

         //   dd($cutOffDay);
            $equipmentDay = EquipmentChargeDay::first()->day ?? null;
            $exemptionPeriod = ExemptionPeriod::first();
          //  dd($equipmentDay);
           // dd($cutOffDay);
            return Inertia::render('Admin/Settings/ServiceVariable/Edit', [
                'cutoffday' => $cutOffDay,
                'equipmentDay' => $equipmentDay,
                'exemptionPeriod' =>$exemptionPeriod,
                'success' => session('success') ?? null,
                'error' => session('error') ?? null,
    
            ]);
        }catch(Exception $e){
            dd($e);
            return Redirect::route('settings')->
            with('error', 'Hubo un error al mostrar las variables de servicio');
        }   
       

    }
    public function updateCutOffDay(Request $request){
        try{
            $validated = $request->validate([
                'day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
            ]);
            $cutOffDay = CutOffDay::updateOrCreate([], ['day' => $validated['day']]);
            $this->changeAllContractEndDate($cutOffDay->day);
            return Redirect::route('settings.service.variable')->
            with('success', 'El Dia de Corte fue Actualizado Con Éxito');
        }catch(Exception $e){
            return Redirect::route('settings.service.variable')->
            with('error', 'Hubo un error al actualizar el dia de corte');
        }
    }
    public function updateEquipmentChargeDay(Request $request)
    {
        try{
            $validated = $request->validate([
                'day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
            ]);
            
            EquipmentChargeDay::updateOrCreate([], ['day' => $validated['day']]);
            //$this->changeAllContractEndDate($equipmentDay->day);
            
            return Redirect::route('settings.service.variable')->
            with('success', 'El dia del cargo del equipo fue Actualizado Con Éxito');
        }catch(Exception $e){
            return Redirect::route('settings.service.variable')->
            with('error', 'Hubo un error al actualizar el dia del cargo de equipo');
        }
    }
    public function changeAllContractEndDate($day)
    {
        try {
            // Validar el día
            if (!is_numeric($day) || $day < 1 || $day > 31) {
                throw new InvalidArgumentException('El día debe ser un número válido entre 1 y 31.');
            }

            // Convertir el día en formato de dos dígitos
            $dayFormatted = str_pad($day, 2, '0', STR_PAD_LEFT);

            //Transacción iniciada
            DB::beginTransaction();

            // Procesar contratos en lotes
            Contract::with('extendContract')->chunk(100, function ($contracts) use ($dayFormatted) {
                foreach ($contracts as $contract) {
                    // Formatear las fechas

                    if($contract->extendContract){

                        if(!$contract->extendContract->status)
                        {
                            $this->newDate($contract, $dayFormatted);
                        }else{
                            $this->newDateWithExtend($contract, $dayFormatted, $contract->extendContract->days);
                        }
                    }else{
                        
                        $this->newDate($contract, $dayFormatted);
                    }
                }
            });
            //Transacción confirmada
            DB::commit();


            Log::info("Las fechas de todos los contratos se actualizaron correctamente.");
        } catch (Exception $e) {
            //Revertir los cambios
            DB::rollBack();

            // Registrar el error
            Log::error("Error al cambiar las fechas de los contratos: " . $e->getMessage());
        }
    }
    public function newDate(Contract $contract, $dayFormatted)
    {
        $contract->start_date = Carbon::parse($contract->start_date)->setDay((int)$dayFormatted);
        $contract->end_date = Carbon::parse($contract->end_date)->setDay((int)$dayFormatted);

        // Guardar cambios
        $contract->save();
    }
    public function newDateWithExtend(Contract $contract, $dayFormatted, $day)
    {
        $endDate = Carbon::parse($contract->end_date);
        $contract->end_date = $endDate->subDays($day)->toDateString();

        $contract->start_date = Carbon::parse($contract->start_date)->setDay((int)$dayFormatted);
        $contract->end_date = Carbon::parse($contract->end_date)->setDay((int)$dayFormatted);

        $contract->end_date = Carbon::parse($contract->end_date)->addDays($day);

        // Guardar cambios
        $contract->save();
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
    public function getEquipmentChargeDay(){
        try{return EquipmentChargeDay::first()->day ?? null;}
        catch(Exception $e){
            Log::info($e);
            return null;
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
