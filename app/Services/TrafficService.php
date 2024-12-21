<?php
namespace App\Services;

use App\Models\Device;
use App\Models\PerformanceDevice;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TrafficService{


    public function service(){

    }

    public function getDay(Device $device){

        try{ 
            return PerformanceDevice::select(
                DB::raw('DATE_FORMAT(created_at, "%H:%i") as time'),   // Formato de hora:minuto para el eje Target
                DB::raw('rate->"$.uplosad" as rate_upload'),
                DB::raw('rate->"$.download" as rate_download'),
                DB::raw('byte->"$.upload" as byte_upload'),
                DB::raw('byte->"$.download" as byte_download')
            )
            ->where('device_id', $device->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();
        }
        catch(Exception $e){
            dd($e);
        }
    }

    public function getWeek(Device $device){

    }
}