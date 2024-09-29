<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Services\UserService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::query()
            ->where('admin', '!=', 1)
            ->selectRaw('id, name, alias, email, admin')
            ->get();
    }


    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Alias',
            'Email',
            'Rol',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->alias ?? 'Sin asignar',
            $user->email,
            UserService::getTypeUser($user->admin)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                ], // Negrita en los encabezados
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => '494848'], // Rojo en hexadecimal
                ],
            ],
        ];
    }

    public function afterSheet(Worksheet $sheet)
    {
        // Ajusta el ancho de todas las columnas automáticamente
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
