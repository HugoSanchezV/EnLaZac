<?php

namespace App\Services;

class RouterService
{
    public function filterNetworksByPrefix($addresses, $networkPrefix): array
    {
        return array_filter($addresses, function ($item) use ($networkPrefix) {
            return isset($item['network']) && strpos($item['network'], $networkPrefix) === 0;
        });
    }

    public function getNetworksNotInDatabase($devices, $db_network): array
    {
        return collect($devices)->filter(function ($device) use ($db_network) {
            return !in_array($device['network'], $db_network);
        })->all();
    }

    public function getDevicesNotInDatabase($devices, $db_devices): array
    {
        return collect($devices)->filter(function ($device) use ($db_devices) {
            return !in_array($device['.id'], array_column($db_devices, '.id'));
        })->all();
    }

    public function getDevicesWithInventorieDevices($devices): array
    {
        foreach ($devices as $device) {
            $device->inventorieDevice = $device->inventorieDevice;
        }

        return $devices;
    }

    public function getDevicesWithUsers($devices): array
    {
        foreach ($devices as $device) {
            $device->user = $device->user;
        }

        return $devices;
    }
}
