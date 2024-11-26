<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ExemptionPeriod;
use App\Models\Installation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateContractDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-contract-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        // Obtener instalaciones asignadas para el día de hoy
        $installations = Installation::with('installationSettings')
            ->whereDate('assigned_date', $today)
            ->get();

        foreach ($installations as $installation) {
            $this->content($installation);
        }


    }

    private function content(Installation $installation)
    {
        $contract = Contract::findOrFail($installation->contract_id);
        $exemptionPeriod = ExemptionPeriod::first();

        $dateInst = Carbon::parse($installation->assigned_date);

        if (!empty($installation->installationSettings) && !empty($installation->installationSettings->exemption_months)) {
            // Usar el mes de la instalación y agregar los meses de exención
            $contract->end_date = $this->adjustEndDate(
                $contract->end_date,
                $dateInst->month,
                $installation->installationSettings->exemption_months
            );
        } else {
            // Determinar el periodo de exención según la fecha de instalación
            if ($dateInst->day >= $exemptionPeriod->start_day && $dateInst->day <= $exemptionPeriod->end_day) {
                $this->info('dentro de los rangos');

                $contract->end_date = $this->adjustEndDate(
                    $contract->end_date,
                    $dateInst->month,
                    $exemptionPeriod->month_next
                );
            } elseif ($dateInst->day > $exemptionPeriod->end_day) {
                $contract->end_date = $this->adjustEndDate(
                    $contract->end_date,
                    $dateInst->month,
                    $exemptionPeriod->month_after_next
                );
            }
        }

        // Guardar cambios en el contrato
        $contract->save();
    }

    /**
     * Ajustar la fecha de fin del contrato
     */
    private function adjustEndDate($endDate, $month, $additionalMonths)
    {
        $date = Carbon::parse($endDate)->setMonth($month);

        $this->info('Se la end date despues de agregarle'.$date);
        // Agregar meses adicionales y validar fecha
        return $date->addMonths($additionalMonths)->toDateString();
    }

}
