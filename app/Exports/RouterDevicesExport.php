<?php

namespace App\Exports;

use App\Models\Router;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RouterDevicesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Cargar el router y sus dispositivos junto con los usuarios e inventario de dispositivos
        $router = Router::with(['devices.user', 'devices.inventorieDevice'])->findOrFail(1);

        // Transformar los dispositivos para incluir los nombres de usuario y dispositivo
        return $router->devices;
    }

    /**
     * Define los encabezados del archivo de Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Internal ID',
            'Device ID',
            'Device Mac',
            'User ID',
            'User Name',
            'Comment',
            'IP Address',
            'Status',
        ];
    }

    /**
     * Mapea los datos para el archivo de Excel
     *
     * @param mixed $device
     * @return array
     */
    public function map($device): array
    {
        return [
            $device->id,
            $device->device_internal_id ?? 'Sin asignar',
            $device->inventorieDevice->id ?? 'Sin asignar', // Nombre del dispositivo
            $device->inventorieDevice->mac_address ?? 'Sin asignar', // Nombre del dispositivo
            $device->user_id ?? 'Sin asignar',
            $device->user->name ?? 'Sin asignar', // Nombre del usuario
            $device->comment,
            $device->address,
            $device->disabled ? 'Inactivo' : 'Activo',
        ];
    }
}
