<?php

namespace App\Services;

use App\Models\InventorieDevice;

class InventorieDeviceService
{
    public static function getChanges(InventorieDevice $device, $validateData)
    {
        $changes = [];
        if ($validateData['mac_address'] !== $device->mac_address) {
            $changes[] = "MAC modificada de {$validateData['mac_address']} a {$device->mac_address}";
        }
        if ($validateData['description'] !== $device->description) {
            $changes[] = "Descripción modificada de '{$validateData['description']}' a '{$device->description}'";
        }
        if ($validateData['brand'] !== $device->brand) {
            $changes[] = "Marca modificada de '{$validateData['brand']}' a '{$device->brand}'";
        }

        return count($changes) > 0 ? implode(', ', $changes) : 'Sin cambios significativos';
    }
}
