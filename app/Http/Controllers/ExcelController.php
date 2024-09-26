<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //

    public function export(Request $request, $model)
    {
        $modelClass = 'App\\Models\\' . ucfirst($model);

        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Modelo no encontrado'], 404);
        }

        // Obtener columnas opcionales (si n00o, se exportan todas)
        $columns = $request->columns;

        // Crear una instancia del modelo
        $modelInstance = new $modelClass;
        return Excel::download(new GenericExport($modelInstance, $columns), $model . '.xlsx');
    }
}
