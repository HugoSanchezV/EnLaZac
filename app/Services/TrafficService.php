<?php
namespace App\Services;

use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrafficService implements ShouldQueue{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function createStats(PerformanceDevice $daily)
    {
        try{
            DB::beginTransaction();
           // $daily = PerformanceDevice::orderBy('created_at', 'desc')->first();
                $this->setWeekly($daily);

                $weekly = $this->getWeekly();
                $this->setMonthly($weekly);

                $monthly = $this->getMonthly();
                $this->setYearly($monthly);
            DB::commit();


        }catch(Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
        }
    }
    private function setYearly(Collection $monthly)
    {
        try{// Obtener el último registro semanal
            $last = PerformanceDeviceYearly::orderBy('created_at', 'desc')->first();

            // Validar si es un nuevo día de la semana o si no hay registros anteriores
            
            if (!$last || Carbon::parse($last->created_at)->monthName != Carbon::parse($monthly->created_at)->monthName) {
                PerformanceDeviceYearly::create([
                    'device_id' => $monthly->device_id,
                    'amount' => 1,
                    'rate' => $monthly->rate,
                    'byte' => $monthly->byte,
                ]);
                return;
            }

            // Actualizar el registro existente
            $rate = $last->rate; // Extraer el JSON actual
            $rate['download'] = $this->operations($rate['download'], $monthly->rate['download'], $last->amount);
            $rate['upload'] = $this->operations($rate['upload'], $monthly->rate['upload'], $last->amount);

            $byte = $last->byte; // Extraer el JSON actual
            $byte['download'] = $this->operations($byte['download'], $monthly->byte['download'], $last->amount);
            $byte['upload'] = $this->operations($byte['upload'], $monthly->byte['upload'], $last->amount);

            $last->update([
                'rate' => $rate,
                'byte' => $byte,
                'amount' => $last->amount + 1, // Incrementar la cantidad
            ]);
        }catch(Exception $e){
            Log::error("Hubo un error en setYearly: ".$e->getMessage());
        }
    }
   
    private function setMonthly(Collection $weekly){
        try{// Obtener el último registro semanal
            $last = PerformanceDeviceMonthly::orderBy('created_at', 'desc')->first();

            // Validar si es un nuevo día de la semana o si no hay registros anteriores
            if (!$last || Carbon::parse($last->created_at)->weekOfYear != Carbon::parse($weekly->created_at)->weekOfYear) {
                PerformanceDeviceMonthly::create([
                    'device_id' => $weekly->device_id,
                    'amount' => 1,
                    'rate' => $weekly->rate,
                    'byte' => $weekly->byte,
                ]);
                return;
            }

            // Actualizar el registro existente
            $rate = $last->rate; // Extraer el JSON actual
            $rate['download'] = $this->operations($rate['download'], $weekly->rate['download'], $last->amount);
            $rate['upload'] = $this->operations($rate['upload'], $weekly->rate['upload'], $last->amount);

            $byte = $last->byte; // Extraer el JSON actual
            $byte['download'] = $this->operations($byte['download'], $weekly->byte['download'], $last->amount);
            $byte['upload'] = $this->operations($byte['upload'], $weekly->byte['upload'], $last->amount);

            $last->update([
                'rate' => $rate,
                'byte' => $byte,
                'amount' => $last->amount + 1, // Incrementar la cantidad
            ]);
        }
        catch(Exception $e){
            Log::error("Hubo un error en setMonthly: ".$e->getMessage());
        }
    }
    private function setWeekly(PerformanceDevice $daily)
    {
        // Obtener el último registro semanal
        try{
            $last = PerformanceDeviceWeekly::orderBy('created_at', 'desc')->first();

            // Validar si es un nuevo día de la semana o si no hay registros anteriores
            if (!$last || Carbon::parse($last->created_at)->dayName != Carbon::parse($daily->created_at)->dayName) {
                PerformanceDeviceWeekly::create([
                    'device_id' => $daily->device_id,
                    'amount' => 1,
                    'rate' => $daily->rate,
                    'byte' => $daily->byte,
                ]);
                return;
            }
    
            // Actualizar el registro existente
            $rate = $last->rate; // Extraer el JSON actual
            $rate['download'] = $this->operations($rate['download'], $daily->rate['download'], $last->amount);
            $rate['upload'] = $this->operations($rate['upload'], $daily->rate['upload'], $last->amount);
    
            $byte = $last->byte; // Extraer el JSON actual
            $byte['download'] = $this->operations($byte['download'], $daily->byte['download'], $last->amount);
            $byte['upload'] = $this->operations($byte['upload'], $daily->byte['upload'], $last->amount);
    
            $last->update([
                'rate' => $rate,
                'byte' => $byte,
                'amount' => $last->amount + 1, // Incrementar la cantidad
            ]);
        }catch(Exception $e){
            Log::error("Hubo un error en setWeekly: ".$e->getMessage());
        }
       
    }


    private function operations($op1, $op2, $amount)
    {
        //op1 = rate['n'] or byte['n']
        //op2 = first parameter (daily | weekly | monthly | yearly) on the function
        $result = (($op1*$amount)+$op2)/$amount;

        return $result;
    }
    private function getMonthly(){
        return PerformanceDeviceMonthly::orderBy('created_at', 'desc')->first();
    }
    private function getWeekly(){
        return PerformanceDeviceWeekly::orderBy('created_at', 'desc')->first();
    }
}

// $last = PerformanceDeviceMonthly::orderBy('created_at', 'desc')->first();
        // if(Carbon::parse($last->created_at)->weekOfYear == Carbon::parse($weekly)->weekOfYear)
        // {
        //     $last->update([

        //     ]);
        // }else{
        //     PerformanceDeviceMonthly::create([
        //         'device_id' => $weekly->device_id,
        //         'amount' => 1,
        //         'rate' => $weekly->rate,
        //         'byte' => $weekly->byte,
        //     ]);
        // }