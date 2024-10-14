<?php

namespace App\Imports;

use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Validation\Rule as ValidationRule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DeviceImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Device([
            'device_internal_id' => $row['id interno'],
            'router_id' => $row['id router'],
            'device_id' => $row['id dispositivo'] ?? null,
            'user_id' => $row['id usuario'] ?? null,
            'comment' => $row['comentario'],
            'address' => $row['id direccion'],
            'list' => 'MOROSOS',
            'disabled' => $row['desactivado'],
        ]);
    }

    public function rules(): array
    {
        return [
            'router_id' => ['required', 'exists:routers,id'],
            'device_id' => ['nullable', 'exists:inventorie_devices,id', ValidationRule::unique('devices', 'device_id')],
            'user_id' => ['nullable', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'ip', ValidationRule::unique('devices', 'address')],
            'disabled' => ['boolean'],
        ];
    }
}
