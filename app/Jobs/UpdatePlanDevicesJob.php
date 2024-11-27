<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Plan;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdatePlanDevicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    public $plan;
    public $devices;

    /**
     * Create a new job instance.
     *
     * @param Plan $plan
     * @param array|Collection $devices
     */
    public function __construct(Plan $plan, $devices)
    {
        $this->plan = $plan;
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
                // Ejecutar el método para aplicar el plan al dispositivo
                $routerOSService->connect($device->router_id);

                $burst_limit = $this->plan->burst_limit['upload_limits'] . '/' . $this->plan->burst_limit['download_limits'];
                $burst_threshold = $this->plan->burst_threshold['upload_limits'] . '/' . $this->plan->burst_threshold['download_limits'];
                $burst_time = $this->plan->burst_time['upload_limits'] . '/' . $this->plan->burst_time['download_limits'];
                $limite_at = $this->plan->limite_at['upload_limits'] . '/' . $this->plan->limite_at['download_limits'];
                $max_limit = $this->plan->max_limit['upload_limits'] . '/' . $this->plan->max_limit['download_limits'];

                $response = $routerOSService->executeCommand('/queue/simple/set', [
                    '.id' => $device->queue_id, // ID de la cola en el router
                    'burst-limit' => $burst_limit,
                    'burst-threshold' => $burst_threshold,
                    'burst-time' => $burst_time,
                    'limit-at' => $limite_at,
                    'max-limit' => $max_limit,
                ]);

                if (!isset($response) || isset($response['!trap'])) {
                    throw new Exception('Error al actualizar el dispositivo: ' . $device->id);
                }

                $routerOSService->disconnect();

                Log::info("Se ha actualizado la conexion " . $device->address);
                // Registrar éxito
                logger()->info("Plan actualizado en el dispositivo {$device->id} exitosamente.");
            } catch (Exception $e) {
                logger()->error("Error al actualizar el dispositivo {$device->id}: " . $e->getMessage());
                // Puedes lanzar la excepción si deseas detener el job en este punto
            }
        }
    }
}

