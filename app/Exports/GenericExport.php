<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Model;

class GenericExport implements FromQuery, WithHeadings
{
    protected $model;
    protected $columns;

    /**
     * Constructor para recibir el modelo y las columnas dinámicamente
     */
    public function __construct(Model $model, array $columns = ['*'])
    {
        $this->model = $model;
        $this->columns = $columns;
    }

    /**
     * Define la consulta con las columnas específicas o todas las columnas si no se especifican
     */
    public function query()
    {
        return $this->model->newQuery()->select($this->columns);
    }

    /**
     * Encabezados basados en las columnas proporcionadas
     */
    public function headings(): array
    {
        if ($this->columns === ['*']) {
            // Si seleccionamos todas las columnas, obtenemos los nombres de los campos del modelo
            return array_keys(\Schema::getColumnListing($this->model->getTable()));
        }

        // Si seleccionamos columnas específicas, usamos los nombres de esas columnas
        return $this->columns;
    }
}
