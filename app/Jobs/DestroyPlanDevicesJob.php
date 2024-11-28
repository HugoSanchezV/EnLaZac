<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DestroyPlanDevicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    public $devices;

    /**
     * Create a new job instance.
     *
     * @param Plan $plan
     * @param array|Collection $devices
     */
    public function __construct($devices)
    {
        $this->devices = $devices;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $routerOSService = RouterOSService::getInstance();

        foreach ($this->devices as $device) {
            try {
                $routerOSService = RouterOSService::getInstance();
                $routerOSService->connect($device->router_id);
    
                // Buscar la cola correspondiente a la IP del dispositivo
                $existingQueue = $routerOSService->executeCommand('/queue/simple/print', [
                    '?=target' => $device->address . '/32',
                    '?=name' => $device->comment 
                ]);
    
                if (isset($existingQueue[0])) {
                    // Si se encuentra una cola, obtener el ID y eliminarla
                    $queueId = $existingQueue[0]['.id'];
                    $response = $routerOSService->executeCommand('/queue/simple/remove', [
                        '.id' => $queueId,
                    ]);
    
                    if (!isset($response) || isset($response['!trap'])) {
                        throw new Exception('Error al eliminar la cola de consumo en red');
                    }
                } else {
                    // No se encontró una cola para el dispositivo
                    Log::info('No se encontro el consumo de la conexion ' .  $device->address);
                }
            } catch (Exception $e) {
                throw new Exception('Error en removeConsumePlanFromDevice: ' . $e->getMessage(), $e->getCode(), $e);
            } finally {
                // Desconexión garantizada
                $routerOSService->disconnect();
            }
        }
    }
}
