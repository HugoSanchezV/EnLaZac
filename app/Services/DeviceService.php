<?php

namespace App\Services;

use App\Http\Controllers\InventorieDevicesController;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Models\Router;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeviceService
{
    public function createDevice(array $row, bool $local)
    {
        try {
            DB::transaction(function () use ($row, $local) {

                try {
                    if (isset($row['device_id'])) {
                        $inv_device = InventorieDevice::findOrFail($row['device_id']); // Sin get()
                        // dd($inv_device->state); 

                        if ($inv_device->state === 1) {
                            throw new Exception('Dispositivo está ocupado.');
                        }
                    }
                } catch (\Exception $e) {
                    throw new Exception(' Dispositivo no encontrado en Inv. u ocupado');
                }

                // Conectar a RouterOS

                $routerOSService = RouterOSService::getInstance();
                $routerOSService->connect($row['router_id']);
                // Ejecutar comando en RouterOS

                $find = $routerOSService->executeCommand('/ip/firewall/address-list/print', [
                    '?list' => 'MOROSOS',  // Filtro por la lista de 'MOROSOS'
                    '?address' => $row['address'],  // Filtro por la IP específica
                ]);


                $disabled = 1;

                if (!empty($find)) {
                    if ($local) {
                        if (!($find[0]['.id'] === $row['device_internal_id'])) {
                            throw new Exception(' La ip '  . $row['address'] . ' usa '
                                . $row['device_internal_id'] . ', pero esto no coincide con los Ids internos');
                        }
                        $disabled = $find[0]['disabled'] ? 0 : 1;
                    } else {
                        throw new Exception(' La ip '  . $row['address'] . ' ya está ocupada');
                    }
                }

                if (!$local) {
                    $response = $routerOSService->executeCommand('/ip/firewall/address-list/add', [
                        'list' => 'MOROSOS',
                        'address' => $row['address'],
                        'comment' => $row['comment'],
                    ]);
                } else {
                    $response = $row['device_internal_id'];
                }

                // Desconectar RouterOS
                $routerOSService->disconnect();

                if (isset($response['!trap'])) {
                    // Manejar el caso de error si falla la operación
                    if ($response['!trap'][0]['message'] === "failure: already have such entry") {
                        throw new Exception(' La ip '  . $row['address'] . ' ya está ocupada');
                    } else {
                        throw new Exception(' Hay un error durante la operacion de router');
                    }
                }

                // Si hay una respuesta válida, crear el dispositivo
                if (!empty($response)) {
                    // dd($validatedData);
                    Device::create([
                        'device_internal_id' => $response, // Corregido el nombre del campo
                        'router_id' => $row['router_id'],
                        'device_id' => $row['device_id'] ?? null,
                        'user_id' => $row['user_id'] ?? null,
                        'comment' => $row['comment'] ?? null, // Verifica si 'comment' existe en el archivo de importación
                        'list' => 'MOROSOS',
                        'address' => $row['address'],
                        'creation_time' => now(),
                        'disabled' => $row['disabled'] ?? $disabled,
                    ]);

                    // Actualizar los contadores en el router
                    $router = Router::findOrFail($row['router_id']);
                    $router->total_devices += 1;
                    $router->enable_devices += 1;
                    $router->save();

                    if (isset($row['device_id'])) {
                        InventorieDevicesController::changeStateDevice($row['device_id'], '1');
                        DeviceHistorie::create([
                            'state' => 1,
                            'comment' => 'Excel: Se ha modificado el estado a "en uso"',
                            'device_id' => $row['device_id'],
                            'user_id' => $device->user_id ?? null,
                            'creator_id' => Auth::id(),
                        ]);
                    }
                } else {
                    throw new Exception('Error al agregar dispostivo en router o al crear el dispositivo.');
                }
            });
        } catch (\Exception $e) {
            // Manejar la excepción según sea necesario
            throw new Exception($e->getMessage());
        }
    }
}
