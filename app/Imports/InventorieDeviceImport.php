<?php

namespace App\Imports;

use App\Models\InventorieDevice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InventorieDeviceImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new InventorieDevice([
            'mac_address' => $row['mac'],
            'description' => $row['descripcion'],
            'brand' => $row['marca'],
            'state' => 0,
        ]);
    }

    /**
     * Define las reglas de validación que se aplicarán a cada fila.
     */
    public function rules(): array
    {
        return [
            'mac' => 'required|unique:inventorie_devices,mac_address|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', // Dirección MAC válida
            'descripcion' => 'string|max:255',
            'marca'       => 'required|string|max:100',
        ];
    }
}
