<?php

namespace App\Console\Commands;

use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        try{
            DB::beginTransaction();
                $this->deleteAllPerformanceDevice();

                $this->deleteOldPerformanceDeviceWeekly();

                $this->deleteOldPerformanceDeviceMonthly();

                $this->deleteOldPerformanceDeviceYearly();
            DB::commit();
        }catch(Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
        }
    }
    private function deleteOldPerformanceDeviceYearly(){
        PerformanceDeviceYearly::where(DB::raw('YEAR(created_at)'), '!=', Carbon::now()->year)->delete();
    }
    private function deleteOldPerformanceDeviceMonthly()
    {
        try{
            $rangeWeek = ((int)Carbon::now()->weekOfYear) - 4;

            if($rangeWeek > 0){
                PerformanceDeviceMonthly::where(DB::raw('WEEKOFYEAR(created_at)'), '<', $rangeWeek)
                ->where(DB::raw('YEAR(created_at)'), '=', Carbon::now()->year)
                ->delete();
            }
        }catch(Exception $e){
            Log::Error("Hubo un error en deleteOldPerformanceDeviceMonthly: ".$e->getMessage());
        }
    }
    private function deleteOldPerformanceDeviceWeekly(){
        try{
            if(Carbon::now()->dayName == 'Monday')
            {
                PerformanceDeviceWeekly::truncate();
            }
        }catch(Exception $e){
            Log::Error("Hubo un error en deleteOldPerformanceDeviceWeekly: ".$e->getMessage());
        }
    }
    private function deleteAllPerformanceDevice(){
        //Borrra todos los registros de la tabla y reinicia el autoincremento
        try{ 
            PerformanceDevice::truncate();
        }catch(Exception $e){
            Log::Error("Hubo un error en deleteAllPerformanceDevice: ".$e->getMessage());
        }
    }
}
