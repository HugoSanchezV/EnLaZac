<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\PerformanceDevice;
use App\Models\User;
use App\Services\TrafficService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
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
                'rateUpload' => $performanceWeek->pluck('avg_rate_upload'),
                'rateDownload' => $performanceWeek->pluck('avg_rate_download'),
                'byteUpload' => $performanceWeek->pluck('total_byte_upload'),
                'byteDownload' => $performanceWeek->pluck('total_byte_download'),
            ];

            $monthPerformance = [
                'labels' => $performanceMonth->pluck('week_number')->map(fn($week) => 'Semana ' . $week),
                'rateUpload' => $performanceMonth->pluck('avg_rate_upload'),
                'rateDownload' => $performanceMonth->pluck('avg_rate_download'),
                'byteUpload' => $performanceMonth->pluck('total_byte_upload'),
                'byteDownload' => $performanceMonth->pluck('total_byte_download'),
            ];

            $yearPerformance = [
                'labels' => $performanceYear->pluck('month'),
                'rateUpload' => $performanceYear->pluck('avg_rate_upload'),
                'rateDownload' => $performanceYear->pluck('avg_rate_download'),
                'byteUpload' => $performanceYear->pluck('total_byte_upload'),
                'byteDownload' => $performanceYear->pluck('total_byte_download'),
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

    private function week($device)
    {
       try{ 
        $subquery = PerformanceDevice::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('DAYNAME(created_at) as day'),
            DB::raw('rate->"$.upload" as rate_upload'),
            DB::raw('rate->"$.download" as rate_download'),
            DB::raw('byte->"$.upload" as byte_upload'),
            DB::raw('byte->"$.download" as byte_download')
        )
        ->where('device_id', $device->id)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->toBase(); // Convierte el query en una subconsulta básica
        
        // Agrupación externa
        return DB::table(DB::raw("({$subquery->toSql()}) as grouped"))
            ->mergeBindings($subquery) // Combina los bindings del subquery
            ->select(
                'day',
                'date',
                DB::raw('AVG(rate_upload) as avg_rate_upload'),
                DB::raw('AVG(rate_download) as avg_rate_download'),
                DB::raw('SUM(byte_upload) as total_byte_upload'),
                DB::raw('SUM(byte_download) as total_byte_download')
            )
            ->groupBy('date', 'day') // Agrupar por fecha y día
            ->orderBy('date', 'asc') // Ordenar por fecha
            ->get();
        
        }
        catch(Exception $e){
            dd($e);
        }
    }

    private function month($device)
    {
       try{ 
            $startDate = Carbon::now()->subWeeks(3)->startOfWeek(); // Las últimas 4 semanas
            $endDate = Carbon::now()->endOfWeek();

            return PerformanceDevice::select(
                DB::raw('YEARWEEK(created_at, 1) as year_week'), // Semana del año
                DB::raw('WEEK(created_at, 1) as week_number'),   // Número de semana en el eje Target
                DB::raw('AVG(rate->"$.upload") as avg_rate_upload'),
                DB::raw('AVG(rate->"$.download") as avg_rate_download'),
                DB::raw('SUM(byte->"$.upload") as total_byte_upload'),
                DB::raw('SUM(byte->"$.download") as total_byte_download')
            )
            ->where('device_id', $device->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('created_at')
            ->orderBy('created_at', 'asc')
            ->get();
        }
        catch(Exception $e){
            dd($e);
        }
    }
        
    private function year($device)
    {
        try
        {
            return PerformanceDevice::select(
                DB::raw('MONTHNAME(created_at) as month'),    // Mes para el eje Target
                DB::raw('MONTH(created_at) as month_number'), // Número de mes para ordenar
                DB::raw('AVG(rate->"$.upload") as avg_rate_upload'),
                DB::raw('AVG(rate->"$.download") as avg_rate_download'),
                DB::raw('SUM(byte->"$.upload") as total_byte_upload'),
                DB::raw('SUM(byte->"$.download") as total_byte_download')
            )
            ->where('device_id', $device->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        }catch(Exception $e){
            dd($e);
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
        PerformanceDevice::create([
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

        $service->createStats();
    }
}
