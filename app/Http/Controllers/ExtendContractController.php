<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ExtendContract;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExtendContractController extends Controller
{
    public function getExtends(){
        try{
            return ExtendContract::with('contract')->get();
        }catch(Exception $e){
            Log::error($e);
            return null;
        }
    }
    public function store($id){
        try{
            ExtendContract::create([
                'contract_id' => $id,
            ]);

        }catch(Exception $e){
            Log::error($e);
        }
    }
    public function extend($id, $day){

        try {
            // Intentar obtener el registro existente
            $extend = ExtendContract::firstOrNew(['contract_id' => $id]);
        
            // Si no existe, establecer los valores iniciales
            if (!$extend->exists) {
                $extend->days = $day;
                $extend->status = true;
                $extend->save();
            } else {
                // Si ya existe, actualizar los valores
                $extend->increment('days', $day);
                $extend->status = true;
                $extend->save();
            }
        } catch (Exception $e) {
            Log::error('Error actualizando contrato extendido: ' . $e->getMessage());
        }
        
    }

    public function shutDownExtend($id)
{
    try {
        // Buscar el contrato
        $contract = Contract::findOrFail($id);

        // Buscar la extensión asociada
        $extend = ExtendContract::where('contract_id', $id)->first();

        // Verificar si existe una extensión
        if (!$extend) {
            Log::warning("No se encontró una extensión asociada al contrato con ID: $id");
            return;
        }

        // Restar días a la fecha de finalización del contrato
        $endDate = Carbon::parse($contract->end_date);
        $contract->end_date = $endDate->subDays($extend->days)->toDateString();

        // Actualizar los datos del contrato y la extensión
        $contract->save();

        $extend->days = 0;
        $extend->status = false;
        $extend->save();

        Log::info("Extensión desactivada correctamente para el contrato con ID: $id");
    } catch (Exception $e) {
        Log::error("Error al desactivar la extensión para el contrato con ID: $id - " . $e->getMessage());
    }
}

    public function changeStatus(){

    }
}
