<?php

namespace App\Console\Commands;

use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old performance stats from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Log::info('Iniciando limpieza de estadísticas de rendimiento.');

            $this->deleteAllPerformanceDevice();
            $this->deleteOldPerformanceDeviceWeekly();
            $this->deleteOldPerformanceDeviceMonthly();
            $this->deleteOldPerformanceDeviceYearly();

            Log::info('Limpieza completada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error durante la limpieza de estadísticas: ' . $e->getMessage());
        }
    }

    private function deleteAllPerformanceDevice()
    {
        try {
            PerformanceDevice::truncate();
            Log::info('Todos los registros de PerformanceDevice han sido eliminados.');
        } catch (Exception $e) {
            Log::error('Error en deleteAllPerformanceDevice: ' . $e->getMessage());
        }
    }

    private function deleteOldPerformanceDeviceWeekly()
    {
        try {
            if (Carbon::now()->dayName === 'Monday') {
                PerformanceDeviceWeekly::truncate();
                Log::info('Todos los registros de PerformanceDeviceWeekly han sido eliminados.');
            }
        } catch (Exception $e) {
            Log::error('Error en deleteOldPerformanceDeviceWeekly: ' . $e->getMessage());
        }
    }

    private function deleteOldPerformanceDeviceMonthly()
    {
        try {
            $startOfAllowedWeeks = Carbon::now()->subWeeks(4)->startOfWeek();

            PerformanceDeviceMonthly::where('created_at', '<', $startOfAllowedWeeks)->delete();

            Log::info('Registros antiguos de PerformanceDeviceMonthly han sido eliminados.');
        } catch (Exception $e) {
            Log::error('Error en deleteOldPerformanceDeviceMonthly: ' . $e->getMessage());
        }
    }

    private function deleteOldPerformanceDeviceYearly()
    {
        try {
            $currentYearStart = Carbon::now()->startOfYear();

            PerformanceDeviceYearly::where('created_at', '<', $currentYearStart)->delete();

            Log::info('Registros antiguos de PerformanceDeviceYearly han sido eliminados.');
        } catch (Exception $e) {
            Log::error('Error en deleteOldPerformanceDeviceYearly: ' . $e->getMessage());
        }
    }
}

