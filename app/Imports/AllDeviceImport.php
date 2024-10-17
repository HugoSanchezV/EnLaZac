<?php

namespace App\Imports;

use App\Models\Device;
use App\Rules\AddressBelongsToNetwork;
use App\Services\DeviceService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AllDeviceImport implements ToModel, WithHeadingRow
{
    protected $service;
    public function __construct(DeviceService $service)
    {
        $this->service = $service;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->validateRowWithRouter($row);
        try {
            $this->service->createDevice($row);
        } catch (\Exception $e) {
            throw new Exception('Error' . $e->getMessage());
        }
    }

    protected function validateRowWithRouter(array $row)
    {

        try {
            $validator = Validator::make($row, [
                // 'device_internal_id' => ['required'], //Rule::unique('devices', 'device_internal_id')], // Asegurarse de que el campo requerido sea correcto
                'device_id' => ['nullable', 'exists:inventorie_devices,id', Rule::unique('devices', 'device_id')],
                'user_id' => ['nullable', 'exists:users,id'],
                'comment' => ['nullable', 'string', 'max:255'], // Cambié "comment" por "comentario" para que coincida
                'address' => [
                    'required',
                    'ip',
                    Rule::unique('devices', 'address'),
                    new AddressBelongsToNetwork($row['router_id']), // Aquí también asegurarse de que sea 'id_router'
                ],
                'router_id' => ['required', 'exists:routers,id'],
                'disabled' => ['required', 'between:0,1'],
            ]);

            // Si la validación falla, lanzar una excepción con el mensaje
            if ($validator->fails()) {
                throw new Exception(implode(', ', $validator->errors()->all()));
            }
        } catch (Exception $e) {
            // dd('Llegue a los errores');
            throw new Exception(implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }
}
