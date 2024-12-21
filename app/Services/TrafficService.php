<?php
namespace App\Services;

use App\Models\Device;
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
    public function createStats()
    {
        try{
            $daily = PerformanceDevice::all();
            $this->setWeekly($daily);

            $weekly = $this->getWeekly();
            $this->setMonthly($weekly);

            $monthly = $this->getMonthly();
            $this->setYearly($monthly);


        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }
    private function setYearly(Collection $daily)
    {
        $last = PerformanceDeviceYearly::orderBy('created_at', 'desc')->first();
        if(Carbon::parse($last->created_at)->weekOfYear == Carbon::parse($daily)->weekOfYear)
        {

        }else{
            PerformanceDeviceYearly::create([
                'device_id'=>$daily->device_id,
            ]);
        }
    }
   
    private function setMonthly(Collection $weekly){
        $last = PerformanceDeviceMonthly::orderBy('created_at', 'desc')->first();
        if(Carbon::parse($last->created_at)->weekOfYear == Carbon::parse($weekly)->weekOfYear)
        {
            $last->update([

            ]);
        }else{
            PerformanceDeviceMonthly::create([
                'device_id' => $weekly->device_id,
            ]);
        }
    }

   

    private function setWeekly(Collection $daily)
    {
        $last = PerformanceDeviceWeekly::orderBy('created_at', 'desc')->first();
        if(Carbon::parse($last->created_at)->dayName == Carbon::parse($daily)->dayName)
        {
            $last->update([

            ]);
        }else{
            //Nuevo registros
            PerformanceDeviceWeekly::create([
                'device_id'=>$daily->device_id,
            ]);
        }

    }
    private function getMonthly(){
        return PerformanceDeviceMonthly::all();
    }
    private function getWeekly(){
        return PerformanceDeviceWeekly::all();
    }
}