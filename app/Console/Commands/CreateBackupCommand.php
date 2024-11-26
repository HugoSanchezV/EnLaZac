<?php
namespace App\Console\Commands;

use App\Http\Controllers\BackupsController;
use Illuminate\Console\Command;
use App\Http\Controllers\YourController; // Asegúrate de cambiar 'YourController' por el nombre correcto del controlador donde está el método createBackup()
use Illuminate\Http\Request;

class CreateBackupCommand extends Command
{
    // Nombre del comando que usarás para ejecutarlo
    protected $signature = 'app:create-backup';

    // Descripción del comando
    protected $description = 'Crear un respaldo de la base de datos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // Crear una instancia del controlador para llamar al método
            $controller = new BackupsController();
            $controller->backup();

            $this->info('Copia de seguridad creada exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al crear la copia de seguridad: ' . $e->getMessage());
        }
    }
}
