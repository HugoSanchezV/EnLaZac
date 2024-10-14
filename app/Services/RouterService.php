<?php

namespace App\Services;

use App\Models\Network;
use App\Models\Router;

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

    public function  isNetworkCorrect(Router $router, $network) {
        $router_networks = $router->netoworks();
    }


    public function getDevicesNotInDatabase($devices, $db_devices): array
    {
        return collect($devices)->filter(function ($device) use ($db_devices) {
            return !collect($db_devices)->pluck('device_internal_id')->contains($device['.id']);
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
