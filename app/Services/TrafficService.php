<?php
namespace App\Services;

use App\Models\PerformanceDevice;
use App\Models\PerformanceDeviceMonthly;
use App\Models\PerformanceDeviceWeekly;
use App\Models\PerformanceDeviceYearly;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrafficService implements ShouldQueue{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function createStats(PerformanceDevice $daily)
    {
        try {
            DB::beginTransaction();

            $this->setWeekly($daily);
            $this->setMonthly($daily);
            $this->setYearly($daily);

            DB::commit();
            //Log::info("Estadísticas generadas exitosamente para el dispositivo ID: {$daily->device_id}");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al crear estadísticas: {$e->getMessage()}");
        }
    }

    /**
     * Genera estadísticas semanales.
     */
    private function setWeekly(PerformanceDevice $daily)
    {
        try {
            $last = PerformanceDeviceWeekly::where('device_id', $daily->device_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$last || $this->isDifferentDay($last->created_at, $daily->created_at)) {
                PerformanceDeviceWeekly::create([
                    'device_id' => $daily->device_id,
                    'amount' => 1,
                    'rate' => $daily->rate,
                    'byte' => $daily->byte,
                    'created_at' => $daily->created_at

                ]);
                return;
            }

            $this->updateStats($last, $daily);
        } catch (Exception $e) {
            Log::error("Error en setWeekly: {$e->getMessage()}");
        }
    }

    /**
     * Genera estadísticas mensuales.
     */
    private function setMonthly(PerformanceDevice $daily)
    {
        try {
            $last = PerformanceDeviceMonthly::where('device_id', $daily->device_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$last || $this->isDifferentWeek($last->created_at, $daily->created_at)) {
                PerformanceDeviceMonthly::create([
                    'device_id' => $daily->device_id,
                    'amount' => 1,
                    'rate' => $daily->rate,
                    'byte' => $daily->byte,
                    'created_at' => $daily->created_at
                ]);
                return;
            }

            $this->updateStats($last, $daily);
        } catch (Exception $e) {
            Log::error("Error en setMonthly: {$e->getMessage()}");
        }
    }

    /**
     * Genera estadísticas anuales.
     */
    private function setYearly(PerformanceDevice $daily)
    {
        try {
            $last = PerformanceDeviceYearly::where('device_id', $daily->device_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$last || $this->isDifferentMonth($last->created_at, $daily->created_at)) {
                PerformanceDeviceYearly::create([
                    'device_id' => $daily->device_id,
                    'amount' => 1,
                    'rate' => $daily->rate,
                    'byte' => $daily->byte,
                    'created_at' => $daily->created_at
                ]);
                return;
            }

            $this->updateStats($last, $daily);
        } catch (Exception $e) {
            Log::error("Error en setYearly: {$e->getMessage()}");
        }
    }

    /**
     * Actualiza las estadísticas existentes.
     */
    private function updateStats($last, PerformanceDevice $daily)
    {
        $rate = $last->rate;
        $rate['download'] = $this->operations($rate['download'], $daily->rate['download'], $last->amount);
        $rate['upload'] = $this->operations($rate['upload'], $daily->rate['upload'], $last->amount);

        $byte = $last->byte;
        $byte['download'] = $this->operations($byte['download'], $daily->byte['download'], $last->amount);
        $byte['upload'] = $this->operations($byte['upload'], $daily->byte['upload'], $last->amount);

        $last->update([
            'rate' => $rate,
            'byte' => $byte,
            'amount' => $last->amount + 1,
        ]);
    }

    /**
     * Operación para combinar estadísticas.
     */
    private function operations($op1, $op2, $amount)
    {
        return (($op1 * $amount) + $op2) / ($amount + 1);
    }

    /**
     * Verifica si dos fechas corresponden a días diferentes.
     */
    private function isDifferentDay($date1, $date2)
    {
        return Carbon::parse($date1)->dayName !== Carbon::parse($date2)->dayName;
    }

    /**
     * Verifica si dos fechas corresponden a semanas diferentes.
     */
    private function isDifferentWeek($date1, $date2)
    {
        return Carbon::parse($date1)->weekOfYear !== Carbon::parse($date2)->weekOfYear;
    }

    /**
     * Verifica si dos fechas corresponden a meses diferentes.
     */
    private function isDifferentMonth($date1, $date2)
    {
        return Carbon::parse($date1)->month !== Carbon::parse($date2)->month;
    }
}