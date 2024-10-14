<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Router;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Support\Facades\DB;

class DeviceService
{
    public function createDevice(array $validatedData)
    {
        try {
            DB::transaction(function () use ($validatedData) {

                // Conectar a RouterOS
                $routerOSService = RouterOSService::getInstance();
                $routerOSService->connect($validatedData['router_id']);

                // Ejecutar comando en RouterOS
                $response = $routerOSService->executeCommand('/ip/firewall/address-list/add', [
                    'list' => 'MOROSOS',
                    'address' => $validatedData['address'],
                    'comment' => $validatedData['comment'],
                ]);

                // Desconectar RouterOS
                $routerOSService->disconnect();

                // Si hay una respuesta válida, crear el dispositivo
                if (!empty($response)) {
                    Device::create([
                        'device_internal_id' => $response,
                        'router_id' => $validatedData['router_id'],
                        'device_id' => $validatedData['device_id'] ?? null,
                        'user_id' => $validatedData['user_id'] ?? null,
                        'comment' => $validatedData['comment'],
                        'address' => $validatedData['address'],
                        'list' => 'MOROSOS',
                        'disabled' => false,
                        'creation_time' => now(),
                    ]);

                    // Actualizar los contadores en el router
                    $router = Router::findOrFail($validatedData['router_id']);
                    $router->total_devices += 1;
                    $router->enable_devices += 1;
                    $router->save();
                }
            });

        } catch (Exception $e) {
            // Manejar la excepción según sea necesario
            throw new Exception('Error al intentar conectar con el router o al crear el dispositivo.');
        }
    }
}
