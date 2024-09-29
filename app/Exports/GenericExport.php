<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromQuery, FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data; // Puede ser una consulta o colección
    protected $headings;
    protected $mappingCallback;

    public function __construct($data, array $headings, callable $mappingCallback)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->mappingCallback = $mappingCallback;
    }

    // Implementación de FromQuery
    public function query()
    {
        if ($this->data instanceof Builder) {
            return $this->data;
        }

        // Devolver una colección vacía si no es una consulta
        return collect();
    }

    // Implementación de FromCollection
    public function collection()
    {
        if ($this->data instanceof Collection) {
            return $this->data;
        }

        // Devolver una colección vacía si no es una colección
        return collect();
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($item): array
    {
        return call_user_func($this->mappingCallback, $item);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => '494848'],
                ],
            ],
        ];
    }

    public function afterSheet(Worksheet $sheet)
    {
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
