<?php

namespace App\Console\Commands;

use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
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
            $this->deleteAllPerformanceDevice();

            $this->deleteOldPerformanceDeviceWeekly();

            $this->deleteOldPerformanceDeviceWeekly();

            $this->deleteOldPerformanceDeviceYearly();
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }
    private function deleteAllPerformanceDevice(){
        //Borrra todos los registros de la tabla y reinicia el autoincremento
        PerformanceDevice::truncate();
    }
}
