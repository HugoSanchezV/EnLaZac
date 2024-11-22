<?php

namespace App\Console\Commands;

use App\Events\ContractWarningEvent;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InstallationSettingsController;
use App\Http\Controllers\ServiceVariablesController;
use App\Services\ChargeService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Contract;
use App\Models\Device;
use App\Models\Charge;
use App\Models\InventorieDevice;
use Exception;

class CheckContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar los contratos que han llegado a su fecha de terminación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $serviceVariable = new ServiceVariablesController();
        $exemption  =  $serviceVariable->getExemptionPeriods();
        $cutoffday = $serviceVariable->getCutOffDay();
        
        

        if($cutoffday && $exemption)
        {    
            

           // $this->info("Dia de corte");
            
            if ($today->day == $serviceVariable->getCutOffDay())
            {
                $controllerContract = new ContractController();
            
                //END_DATE es igual a la fecha de corte
                $service = new ChargeService();
            
                $installationSt = new InstallationSettingsController();
                $contractTerms = $controllerContract->getContracts($today);
                $this->info($contractTerms->count());

                foreach($contractTerms as $contract){
                    $endDate = Carbon::parse($contract->end_date);
                    //if($contract->active == 1){
                    self::checkInstallation($contract, $today, $endDate, $service, $exemption, $installationSt);
                   //}
                }
            }else if($today->day == 25){
                $controllerContract = new ContractController();
            
                $service = new ChargeService();

                $contractTerms = $controllerContract->getContracts($today);
                foreach($contractTerms as $contract){
                    $endDate = Carbon::parse($contract->end_date);
                    if($contract->active == 1){
                        if($endDate < $today){
                            self::cargoPorPago($service, $contract);

                        }
                    }
                }
            }
            //Extender condición para los contratos a los que se le extendió el día de pago

        }else{
            $this->info('No se ha ingresado el dia de corte y los dias de excepcion de plazos de primer pago');
        }

   //     $this->info('Se han verificado los contratos.');
    }

    private function conditional($endDate, $contract, $today, $service)
    {
        if(($endDate->year == $today->year)&&($endDate->month == $today->month)){

            if((($endDate->day)+2) == $today->day)
            {
                $this->info("Envio de email en 2 dias");
                //Enviar correo
                self::sendEmail($contract, "2");

            }else if((($endDate->day)+7) == $today->day){
                $this->info("Envio de email en 2 dias");
                self::sendEmail($contract, "7");

            } else if ($endDate->day == $today->day)
            {
                $this->info("Fin del contrato y cargo");
                
                //Cortar internet
                self::disconectUser($contract);
                //Generar cargo

                self::Extra_charge($service, $contract);
            }
        }else if($today->month == 1)
        {
            if(($today->day == 1)
            &&(($today->year > $endDate->year)
            &&($today->month < $endDate->month)))
            {
                $this->info("Cargo por renta");
                self::cargoPorPago($service, $contract);
            }
        }else{
            if(($today->day == 1)
            &&(($endDate->year == $today->year)
            &&($today->month > $endDate->month)))
            {
                $this->info("Cargo por renta");
                self::cargoPorPago($service, $contract);
            }
        }
    }
    private function checkInstallation($contract, $today, $endDate, $service, $exemption, $installationSt)
    {

        //Verificar si el contrato tiene una instalación asignada
        if (!$contract->installations->isEmpty()){

            //Iterar cada instalación asignada al contrato
            foreach($contract->installations as $installation)
            {
                $assigned = Carbon::parse($installation->assigned_date);

                //Después del 16 -> personalizable
                //if($assigned->year >= $today->year){
                // BUSCAR SI LA INSTALACION TIENE OTRO MES CONFIGURADO 
                
                if(($assigned->day >= $exemption->end_day))
                {
                    $months = $installationSt->getExemptionMonth($installation->id);
                    if($months){
                        $exemptionMonth = $months;
                    }else{
                        $exemptionMonth = $exemption->month_after_next;
                    }
                    
                    //Le cobra no el proximo mes, sino el siguiente
                    if(self::incomingMonth(
                    $assigned, 
                    $today, 
                    $exemptionMonth, 
                    )){
                        self::conditional($endDate, $contract, $today, $service);
                    }

                }else if (($assigned->day >= $exemption->start_day)&&($assigned->day <= $exemption->end_day)){
                    
                    $months = $installationSt->getExemptionMonth($installation->id);

                    if($months){
                        $exemptionMonth = $months;
                    }else{
                        $exemptionMonth = $exemption->month_next;
                    }
                    
                    //Condicionar si paga el siguiente mes o  el proximo
                    if(self::incomingMonth(
                    $assigned, 
                    $today, 
                    $exemptionMonth, 
                    ))
                    {
                        self::conditional($endDate, $contract, $today, $service);
                    }
                }else{
                    self::conditional($endDate, $contract, $today, $service);
                }
               // }
            }
        }else{
            self::conditional($endDate, $contract, $today, $service);
        }
    }
    private function incomingMonth($assigned, $today, $increase){
        /* 
            Comparación si después los meses que se excluyo el pago de servicio
            son iguales al mes actual
        */ 

        $finalMonth = $assigned->month + $increase;
        $year = $assigned->year;

        //Obtener el mes y el año de acuerdo al incremento del rango de fechas
        if($finalMonth > 12)
        {
            $year = $year + intval($finalMonth / 12);
            while($finalMonth > 12)
                $finalMonth -= 12;   
        }

        if(($year == $today->year))
        {
            if(($finalMonth <= $today->month))
            {
                //Mes cedido
                return false;
            }
               
        /*
            Comparación en caso de que la fecha de instalación es a finales de año
        */
        }else if($year > $today->year)
        {
            //Mes cedido
            return false;
  
        }
        return true;
    }

    private function cargoPorPago($service , Contract $contract)
    {$service->createChargeCourtDate($contract);}

    public function extra_charge($service, Contract $contract)
    {$service->createChargeMounthsDebt($contract);}

    private function sendEmail($contract, $days)
    {event(new ContractWarningEvent($contract, $days));}

    private function disconectUser($contract)
    {
        $deviceController = new DevicesController();
        
        $deviceController->disconectUser($contract);

            //Determinar bien 
        $this->info('\n SE DESCONECTO AL USUARIO.\n');
    }
}
