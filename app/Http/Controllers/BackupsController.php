<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Backups;
use App\Services\RequestService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class BackupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Backup::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('path', 'like', "%$search%")
                    ->orwhere('id', 'like', "%$search%")
                    ->orwhere('user_id', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'desc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $backups = $query->latest()->paginate(8)->through(function ($item) {
            $date = Carbon::parse($item->created_at);
            return [
                'id' => $item->id,
                'path' => $item->path,
                'user_id' => $item->user->name ?? "Sin asignar",
                'created_at' => $date->diffForHumans(),
            ];
        });

        $totalBackupCount = Backup::count();

        return Inertia::render('Admin/Settings/Backups/Index', [
            'backups' => $backups,
            'pagination' => [
                'links' => $backups->links()->elements[0],
                'next_page_url' => $backups->nextPageUrl(),
                'prev_page_url' => $backups->previousPageUrl(),
                'per_page' => $backups->perPage(),
                'total' => $backups->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalBackupCount' => $totalBackupCount,
            'q' => "",
            'attribute' => "id",
            'type' => "todos",
            'order' => "desc",
        ]);
    }

    public function destroy(Backup $backup, Request $request)
    {
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $filePath = storage_path('app/backup/' . $backup->path);

            if (File::exists($filePath)) {
                File::delete($filePath);
            } else {
                return Redirect::route('backups', $data)->with('error', 'El archivo de backup no existe.');
            }
            $backup->delete();
            return Redirect::route('backups', $data)->with('success', 'Backup eliminado con éxito ');
        } catch (Exception $e) {
            return Redirect::route('backups', $data)->with('error', 'Error al eliminar Backup');
        }
    }

    public function clear()
    {
        try {
            // Obtener la ruta de la carpeta de backups
            $backupFolder = storage_path('app/backup');

            // Comprobar si la carpeta existe
            if (File::exists($backupFolder)) {
                // Eliminar todos los archivos dentro de la carpeta de backups
                File::deleteDirectory($backupFolder, true);

                // Volver a crear la carpeta vacía después de eliminarla
                File::makeDirectory($backupFolder, 0755, true);
            }

            // Eliminar todos los registros de la tabla backups
            Backup::truncate();

            return Redirect::route('backups')->with('success', 'Todos los backups han sido eliminados con éxito.');
        } catch (Exception $e) {
            return Redirect::route('backups')->with('error', 'Error al limpiar los backups: ' . $e->getMessage());
        }
    }

    public function createBackup(Request $request)
    {
        try {
            $gzip_file_name = self::backup();
            return Redirect::route('backups', RequestService::getArrayIndexRequest($request))->with('success', 'Copia de seguridad creada exitosamente en ' . $gzip_file_name);
        } catch (\Exception $e) {
            return Redirect::route('backups')->with('error', 'No se pudo crear la copia de seguridad: ' . $e->getMessage());
        }
    }

    public function backup()
    {
        try {
            $databaseName = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');

            $date = now()->format('d-Y-M-H-i');
            $gzip_file_name = $databaseName  . "_" .  $date . "_backup_" . Str::random(8) . ".sql.gz";
            $backup_directory = storage_path("app/backup");
            $gzip_file_path = "$backup_directory/$gzip_file_name";

            Log::info("Ruta de archivo de respaldo: $gzip_file_path");

            if (!is_dir($backup_directory)) {
                mkdir($backup_directory, 0755, true);
                Log::info("Carpeta de respaldo creada: $backup_directory");
            }

            // Ejecutar el comando mysqldump y comprimir con gzip
            $command = "mysqldump --user={$username} --password={$password} --host={$host} {$databaseName} --single-transaction --quick --lock-tables=false | gzip > {$gzip_file_path}";

            // Ejecutar el comando en la terminal
            exec($command . " 2>&1", $output, $result);

            // Verificar si hubo un error en la ejecución del comando
            if ($result !== 0) {
                Log::error('Error al crear el respaldo: ' . implode("\n", $output));
                throw new \Exception('Error al crear el respaldo de la base de datos: ' . implode("\n", $output));
            } else {
                // Guardar la referencia del backup en la base de datos
                Backup::create([
                    "path" => $gzip_file_name,
                    "user_id" => Auth::id() ?? null,
                ]);

                Log::info('Copia de seguridad guardada en: ' . $gzip_file_path);
            }

            return $gzip_file_name;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function download(Backup $backup, Request $request)
    {
        try {
            if (Storage::exists('backup/' . $backup->path)) {
                $path = Storage::path('backup/' . $backup->path);
                return response()->download($path);
            }
        } catch (Exception $e) {
            return Redirect::route('backups', RequestService::getArrayIndexRequest($request))->with('error', 'No se ha podido realizar la descarga');
        }
    }
}
