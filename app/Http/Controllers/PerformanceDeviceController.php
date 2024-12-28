<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use App\Models\User;
use App\Services\TrafficService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PerformanceDeviceController extends Controller
{
    public function indexByUser($id)
    {
        // 
        $device = Device::where('user_id', $id)->first();
        if (!$device) {
            // Manejar el caso donde el dispositivo no existe
            return redirect()->back()->with('success', 'No se ha asignado un dispositivo al usuario');
        }else{

            $performances = PerformanceDevice::where('device_id', $device->id)->get();

            // Sumar el total de bytes subidos y descargados
            $totalByteUpload = $performances->sum(function ($performance) {
                return $performance->byte['upload'] ?? 0; // Suma solo si 'upload' existe en el array
            });

            $totalByteDownload = $performances->sum(function ($performance) {
                return $performance->byte['download'] ?? 0; // Suma solo si 'download' existe en el array
            });
            $totalRateUpload = $performances->sum(function ($performance) {
                return $performance->byte['upload'] ?? 0; // Suma solo si 'upload' existe en el array
            });

            $totalRateDownload = $performances->sum(function ($performance) {
                return $performance->byte['download'] ?? 0; // Suma solo si 'download' existe en el array
            });

            // Hacer los periodos 

            $performanceToday = self::today($device);
            $performanceWeek = self::week($device);
            $performanceMonth = self::month($device);
            $performanceYear = self::year($device);

            $todayPerformance = [
                'labels' => $performanceToday->pluck('time'),
                'rateUpload' => $performanceToday->pluck('rate_upload'),
                'rateDownload' => $performanceToday->pluck('rate_download'),
                'byteUpload' => $performanceToday->pluck('byte_upload'),
                'byteDownload' => $performanceToday->pluck('byte_download'),
            ];

            $weekPerformance = [
                'labels' => $performanceWeek->pluck('day'),
                'rateUpload' => $performanceWeek->pluck('avg_rate_upload'),
                'rateDownload' => $performanceWeek->pluck('avg_rate_download'),
                'byteUpload' => $performanceWeek->pluck('avg_byte_upload'),
                'byteDownload' => $performanceWeek->pluck('avg_byte_download'),
            ];

            $monthPerformance = [
                'labels' => $performanceMonth->pluck('week_number')->map(fn($week) => 'Semana ' . $week),
                'rateUpload' => $performanceMonth->pluck('avg_rate_upload'),
                'rateDownload' => $performanceMonth->pluck('avg_rate_download'),
                'byteUpload' => $performanceMonth->pluck('avg_byte_upload'),
                'byteDownload' => $performanceMonth->pluck('avg_byte_download'),
            ];

            $yearPerformance = [
                'labels' => $performanceYear->pluck('month'),
                'rateUpload' => $performanceYear->pluck('avg_rate_upload'),
                'rateDownload' => $performanceYear->pluck('avg_rate_download'),
                'byteUpload' => $performanceYear->pluck('avg_byte_upload'),
                'byteDownload' => $performanceYear->pluck('avg_byte_download'),
            ];

            
            return Inertia::render('Admin/PerformanceDevice/PerformanceDevice', [
                'device' => $device,
                'todayPerformance' => $todayPerformance,
                'weekPerformance' => $weekPerformance,
                'monthPerformance' => $monthPerformance,
                'yearPerformance' => $yearPerformance,
                'totalByteUpload'=>$totalByteUpload,
                'totalByteDownload'=>$totalByteDownload,
                'totalRateUpload'=>$totalRateUpload ,
                'totalRateDownload'=>$totalRateDownload,

                
                'success' => session('success') ?? null,
                'error' => session('error') ?? null,
                'warning' => session('warning') ?? null,

            ]);
        }
        
    }
    public function indexByDevice($id)
    {
      
        $device = Device::where('id', $id)->first();
        if (!$device) {
            // Manejar el caso donde el dispositivo no existe
            return redirect()->back()->with('success', 'Dispositivo no encontrado.');
        }else{
            if($device->user_id){

                $user = User::findOrFail($device->user_id);
            }else{
                $user= null;
            }

            $performances = PerformanceDevice::where('device_id', $device->id)->get();

            // Sumar el total de bytes subidos y descargados
            $totalByteUpload = $performances->sum(function ($performance) {
                return $performance->byte['upload'] ?? 0; // Suma solo si 'upload' existe en el array
            });

            $totalByteDownload = $performances->sum(function ($performance) {
                return $performance->byte['download'] ?? 0; // Suma solo si 'download' existe en el array
            });
            $totalRateUpload = $performances->sum(function ($performance) {
                return $performance->rate['upload'] ?? 0; // Suma solo si 'upload' existe en el array
            });

            $totalRateDownload = $performances->sum(function ($performance) {
                return $performance->rate['download'] ?? 0; // Suma solo si 'download' existe en el array
            });

            // Hacer los periodos 
         
            $performanceToday = self::today($device);
         
            $performanceWeek = self::week($device);
           // dd();
            $performanceMonth = self::month($device);
            
            $performanceYear = self::year($device);
            
           // dd($performanceWeek);

            $todayPerformance = [
                'labels' => $performanceToday->pluck('time'),
                'rateUpload' => $performanceToday->pluck('rate_upload'),
                'rateDownload' => $performanceToday->pluck('rate_download'),
                'byteUpload' => $performanceToday->pluck('byte_upload'),
                'byteDownload' => $performanceToday->pluck('byte_download'),
            ];
           // dd($todayPerformance);
            $weekPerformance = [
                'labels' => $performanceWeek->pluck('day'),
                'rateUpload' => $performanceWeek->pluck('rate_upload'),
                'rateDownload' => $performanceWeek->pluck('rate_download'),
                'byteUpload' => $performanceWeek->pluck('byte_upload'),
                'byteDownload' => $performanceWeek->pluck('byte_download'),
            ];

            $monthPerformance = [
                'labels' => $performanceMonth->pluck('week_number')->map(fn($week) => 'Semana ' . $week),
                'rateUpload' => $performanceMonth->pluck('rate_upload'),
                'rateDownload' => $performanceMonth->pluck('rate_download'),
                'byteUpload' => $performanceMonth->pluck('byte_upload'),
                'byteDownload' => $performanceMonth->pluck('byte_download'),
            ];

            $yearPerformance = [
                'labels' => $performanceYear->pluck('month'),
                'rateUpload' => $performanceYear->pluck('rate_upload'),
                'rateDownload' => $performanceYear->pluck('rate_download'),
                'byteUpload' => $performanceYear->pluck('byte_upload'),
                'byteDownload' => $performanceYear->pluck('byte_download'),
            ];

            // $filteredPerformance = collect($yearPerformance['labels'])->map(function ($month, $index) use ($yearPerformance) {
            //     return [
            //         'month' => $month,
            //         'rateUpload' => $yearPerformance['rateUpload'][$index],
            //         'rateDownload' => $yearPerformance['rateDownload'][$index],
            //         'byteUpload' => $yearPerformance['byteUpload'][$index],
            //         'byteDownload' => $yearPerformance['byteDownload'][$index],
            //     ];
            // })->filter(function ($data) {
            //     // Filtrar meses con datos válidos (excluyendo nulos o ceros)
            //     return $data['rateUpload'] !== null || $data['rateDownload'] !== null || $data['byteUpload'] !== null || $data['byteDownload'] !== null;
            // }); 
            // // Reorganizar el arreglo en el formato deseado
            // $yearPerformance = [
            //     'labels' => $filteredPerformance->pluck('month'),
            //     'rateUpload' => $filteredPerformance->pluck('rateUpload'),
            //     'rateDownload' => $filteredPerformance->pluck('rateDownload'),
            //     'byteUpload' => $filteredPerformance->pluck('byteUpload'),
            //     'byteDownload' => $filteredPerformance->pluck('byteDownload'),
            // ];
         //   dd($yearPerformance);
            return Inertia::render('Admin/PerformanceDevice/PerformanceDevice', [
                'device' => $device,
                'user' => $user,
                'todayPerformance' => $todayPerformance,
                'weekPerformance' => $weekPerformance,
                'monthPerformance' => $monthPerformance,
                'yearPerformance' => $yearPerformance,
                'totalByteUpload'=>$totalByteUpload,
                'totalByteDownload'=>$totalByteDownload,
                'totalRateUpload'=>$totalRateUpload ,
                'totalRateDownload'=>$totalRateDownload,
                
                'success' => session('success') ?? null,
                'error' => session('error') ?? null,
                'warning' => session('warning') ?? null,

            ]);
        }
    }
    private function today($device){
        // Consulta para el día de hoy, agrupado por hora y minuto exacto
        
       try{ 
        $columns = "
            TO_CHAR(created_at, 'HH24:MI') AS time, 
            (rate->>'upload')::float AS rate_upload, 
            (rate->>'download')::float AS rate_download, 
            (byte->>'upload')::float AS byte_upload, 
            (byte->>'download')::float AS byte_download
        ";

        return PerformanceDevice::selectRaw($columns)
            ->where('device_id', $device->id) // Reemplaza con el ID del dispositivo
            ->whereRaw('DATE(created_at) = ?', [now()->toDateString()]) // Filtra por la fecha actual
            ->orderBy('created_at', 'asc') // Ordena por la fecha de creación
            ->get();
            // return PerformanceDevice::select(
            //     DB::raw('DATE_FORMAT(created_at, "%H:%i") as time'),   // Formato de hora:minuto para el eje Target
            //     DB::raw('rate->"$.upload" as rate_upload'),
            //     DB::raw('rate->"$.download" as rate_download'),
            //     DB::raw('byte->"$.upload" as byte_upload'),
            //     DB::raw('byte->"$.download" as byte_download')
            // )
            // ->where('device_id', $device->id)
            // ->whereDate('created_at', Carbon::today())
            // ->orderBy('created_at', 'asc')
            // ->get();
        }
        catch(Exception $e){
            Log::info($e);

        }
    }

    private function week($device)
    {
       try{ 

        return 
        PerformanceDeviceWeekly::selectRaw("
            created_at::date as date, 
            TO_CHAR(created_at, 'FMDay') as day, 
            COALESCE((rate->>'upload')::float, 0) as rate_upload, 
            COALESCE((rate->>'download')::float, 0) as rate_download, 
            COALESCE((byte->>'upload')::float, 0) as byte_upload, 
            COALESCE((byte->>'download')::float, 0) as byte_download
        ")
        // PerformanceDeviceWeekly::select(
        //     DB::raw('DATE(created_at) as date'),   // Formato de hora:minuto para el eje Target
        //     DB::raw('DAYNAME(created_at) as day'),
        //     DB::raw('rate->"$.upload" as rate_upload'),
        //     DB::raw('rate->"$.download" as rate_download'),
        //     DB::raw('byte->"$.upload" as byte_upload'),
        //     DB::raw('byte->"$.download" as byte_download')
        // )
        ->where('device_id', $device->id)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->orderBy('created_at', 'asc')
        ->get();
        
        }
        catch(Exception $e){
            Log::info($e);

        }
    }

    private function month($device)
    {
       try{ 
            $startDate = Carbon::now()->subWeeks(3)->startOfWeek(); // Las últimas 4 semanas
            $endDate = Carbon::now()->endOfWeek();

            return PerformanceDeviceMonthly::selectRaw("
                TO_CHAR(created_at, 'IYYY-IW') as year_week, -- Año y semana ISO
                EXTRACT(WEEK FROM created_at)::int as week_number, -- Número de semana
                (rate->>'upload')::float as rate_upload,
                (rate->>'download')::float as rate_download,
                (byte->>'upload')::float as byte_upload,
                (byte->>'download')::float as byte_download
            ")
            ->where('device_id', $device->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

            // return PerformanceDevice::select(
            //     DB::raw('YEARWEEK(created_at, 1) as year_week'), // Semana del año
            //     DB::raw('WEEK(created_at, 1) as week_number'),   // Número de semana en el eje Target
            //     DB::raw('AVG(rate->"$.upload") as avg_rate_upload'),
            //     DB::raw('AVG(rate->"$.download") as avg_rate_download'),
            //     DB::raw('SUM(byte->"$.upload") as total_byte_upload'),
            //     DB::raw('SUM(byte->"$.download") as total_byte_download')
            // )
            // ->where('device_id', $device->id)
            // ->whereBetween('created_at', [$startDate, $endDate])
            // ->groupBy('created_at')
            // ->orderBy('created_at', 'asc')
            // ->get();
        }
        catch(Exception $e){
            Log::info($e);

        }
    }
        
    private function year($device)
    {
        try
        {
            try{ 
                return PerformanceDeviceYearly::selectRaw("
                    TO_CHAR(created_at, 'FMMonth') as month, -- Nombre del mes (e.g., January)
                    EXTRACT(MONTH FROM created_at)::int as month_number, -- Número del mes (e.g., 1 para enero)
                    (rate->>'upload')::float as rate_upload, -- Extraer y convertir a número
                    (rate->>'download')::float as rate_download,
                    (byte->>'upload')::float as byte_upload,
                    (byte->>'download')::float as byte_download
                ")
                ->where('device_id', $device->id)
                ->orderBy('created_at', 'asc')
                ->get();
               // dd($hola);
            }
            catch(Exception $e){
                Log::info($e);
            }

            // return PerformanceDevice::select(
            //     DB::raw('MONTHNAME(created_at) as month'),    // Mes para el eje Target
            //     DB::raw('MONTH(created_at) as month_number'), // Número de mes para ordenar
            //     DB::raw('AVG(rate->"$.upload") as avg_rate_upload'),
            //     DB::raw('AVG(rate->"$.download") as avg_rate_download'),
            //     DB::raw('SUM(byte->"$.upload") as total_byte_upload'),
            //     DB::raw('SUM(byte->"$.download") as total_byte_download')
            // )
            // ->where('device_id', $device->id)
            // ->whereYear('created_at', Carbon::now()->year)
            // ->groupBy('created_at')
            // ->orderBy('created_at', 'asc')
            // ->get();

        }catch(Exception $e){
            Log::info($e);

        }
    }

    public function store(array $request){

        $service = new TrafficService(); 
        //Añadir validaciones "manuales"
        Validator::make($request, [
            'device_id' => ['required', 'exists:devices,id'],
            'rate.upload' => ['required', 'numeric'],
            'rate.download' => ['required', 'numeric'],
            'byte.upload' => ['required', 'numeric'],
            'byte.download' => ['required', 'numeric'],
        ])->validate();
        
        // Crear el PerformanceDevice con los datos validados
        $recentDaily = PerformanceDevice::create([
            'device_id' => $request['device_id'],
            'rate' => [
                'upload' => $request['rate']['upload'],   // Acceder a datos anidados correctamente
                'download' => $request['rate']['download'],
            ],
            'byte' => [
                'upload' => $request['byte']['upload'],   // Acceder a datos anidados correctamente
                'download' => $request['byte']['download'],
            ],
        ]);

        return $recentDaily;
        $service->createStats($recentDaily);
    }
}
