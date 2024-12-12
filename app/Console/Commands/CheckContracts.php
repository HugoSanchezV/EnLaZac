<?php

namespace App\Console\Commands;

use App\Events\ContractWarningEvent;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentSanctionController;
use App\Http\Controllers\ServiceVariablesController;
use App\Services\ChargeService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Contract;
use App\Models\PaymentSanction;
use Exception;
use MercadoPago\Resources\Payment\Card;

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

        // Obtener variables de servicio
        $serviceVariable = new ServiceVariablesController();
        $exemption = $serviceVariable->getExemptionPeriods();
        $cutoffDay = $serviceVariable->getCutOffDay();
        $equipmentDay = $serviceVariable->getEquipmentChargeDay();

        // Validar si todas las variables necesarias están configuradas
        if (!$this->areVariablesConfigured($cutoffDay, $exemption, $equipmentDay)) {
            $this->info('No se ha ingresado el día de corte ni los días de excepción de plazos de primer pago.');
            return;
        }

        // Procesar contratos según el día actual
        if ($today->day == $cutoffDay) 
        {
            $this->processContracts($today, $exemption, new ChargeService());
        } elseif ($today->day == $equipmentDay) 
        {
         //   $this->info('Procesando contratos para el día de cobro de equipo.');
            $this->processContracts($today, $exemption, new ChargeService(), $equipmentDay);
        }else if($today->day == '1') 
        {
            $this->sanctionContracts();
        }
    }

    /**
     * Verifica si todas las variables necesarias están configuradas
     */
    private function sanctionContracts(){

        $controllerSanction = new PaymentSanctionController();
        $sanctions = $controllerSanction->getSanction();

        foreach($sanctions as $sanction){
            self::disconectUser($sanction->contract); // Cortar internet
        }
    }
    private function applySanction($id){
        
        $contract = Contract::findOrFail($id);
        $day = Carbon::parse($contract->end_date);
        if($day == '1'){
            
        }

        
    }

    private function areVariablesConfigured($cutoffDay, $exemption, $equipmentDay): bool
    {
        return $cutoffDay && $exemption && $equipmentDay;
    }

    /**
     * Procesar los contratos según la lógica definida
     */
    private function processContracts(Carbon $today, $exemption, ChargeService $service, $equipmentDay = null)
    {
        $controllerContract = new ContractController();
        
        $contractTerms = $controllerContract->getContracts();

        foreach ($contractTerms as $contract) 
        {
            $endDate = Carbon::parse($contract->end_date);

            $this->conditional($endDate, $contract, $today, $service, $equipmentDay);
        }
    }


    private function conditional(Carbon $endDate, Contract $contract, Carbon $today, ChargeService $service, $equipmentDay)
    {
        $endDate = $endDate->startOfDay();
        $today = $today->startOfDay();
        // Verificar si el contrato está en el mismo mes y año

        $payment = new PaymentSanction();

        $payment = PaymentSanction::where('contract_id', $contract->id)->first();

        if ($endDate->month ==($today->month) && $endDate->year == ($today->year)) {
            // Acciones según el día
          
            if ($endDate->day == $today->day + 2) {
               // $this->info("Envio de email en 2 días");
                self::sendEmail($contract, "2");
            } elseif ($endDate->day == $today->day + 7) {
               // $this->info("Envio de email en 7 días");
                self::sendEmail($contract, "7");

            } elseif ($endDate->day == ($today->day)) {
                self::cargoPorPago($service, $contract); // Cargo adicional

                $this->info("Fin del contrato y cargo con todos el end day actual");
                //self::disconectUser($contract); // Cortar internet
                ///self::Extra_charge($service, $contract); // Generar cargo
            } elseif ($today->day === $equipmentDay)  {
                $this->info("Segundo cargo con todos el end day actual");
                self::Extra_charge($service, $contract); // Generar cargo
                self::setSanction($contract->id);
            }
        }
        // Verificar si el contrato está en el pasado o en un mes anterior
        elseif ($endDate->isPast() || ($endDate->year == $today->year && $endDate->month < $today->month)) 
        {  
           // $this->info($equipmentDay);
            if ($endDate->day == ($today->day)) {
                //$this->info("Cargo first");
                self::cargoPorPago($service, $contract); // Cargo adicional

              //  self::Extra_charge($service, $contract);
            } elseif ($today->day === $equipmentDay) {
                //$this->info("Cargo");
                self::Extra_charge($service, $contract); // Generar cargo
            }
        }
    }
    private function setSanction($id)
    {
        $controller = new PaymentSanctionController();
        $controller->fromPayment($id);
    }

    // private function isEquipmentDaySet($equipmentDay): bool
    // {
    //     // Verificar si $equipmentDay tiene propiedades configuradas
    //     return !is_null($equipmentDay);
    // }

    // private function checkInstallation(
    //     Contract $contract,
    //     Carbon $today,
    //     Carbon $endDate,
    //     ChargeService $service,
    //     ExemptionPeriod $exemption
    // ) {
    //     $equi = new EquipmentChargeDay();
    
    //     // Verificar si el contrato tiene instalaciones asignadas
    //     if ($contract->installations->isEmpty()) {
    //         $this->info("Directo");
    //         self::conditional($endDate, $contract, $today, $service, $equi);
    //         return;
    //     }
    
    //     foreach ($contract->installations as $installation) {
    //         $assigned = Carbon::parse($installation->assigned_date);
    
    //         // Determinar mes de exención según configuración
    //         $exemptionMonth = $this->getExemptionMonth($assigned, $installation, $exemption);
    //         $this->info("ExemptionMonth: ".$exemptionMonth);
    //         // Verificar si está dentro del mes configurado para aplicar lógica
    //         if($exemptionMonth != 0)
    //         {
    //             if (self::incomingMonth($assigned, $today, $exemptionMonth)) {
    //                 $this->info("Ya no coincide");
    //                 self::conditional($endDate, $contract, $today, $service, $equi);
    //             }else{
    //                 $this->info("No paga por la excepcion de inslatación, niega la opción de aca");
    //             }
    //         }else{
    //             $this->info("No entra en los rango por lo que debe pagars en fa");

    //             self::conditional($endDate, $contract, $today, $service, $equi);
    //         }

    //     }
    // }
    
    // /**
    //  * Determinar el mes de exención basado en la instalación y el periodo de exención
    //  */
    // private function getExemptionMonth(
    //     Carbon $assigned,
    //     $installation,
    //     ExemptionPeriod $exemption,
    // ) {
    //     // Si el día asignado es mayor o igual al día de fin de exención
    //     if ($assigned->day > $exemption->end_day) {
    //         return $installation->installationSettings->exemption_months 
    //             ?? $exemption->month_after_next;
    //     }
    
    //     // Si el día asignado está dentro del rango de exención
    //     if ($assigned->day >= $exemption->start_day && $assigned->day <= $exemption->end_day) {
    //         return $installation->installationSettings->exemption_months 
    //             ?? $exemption->month_next;
    //     }
    
    //     // Caso por defecto
    //     return 0;
    // }
    
    // private function incomingMonth($assigned, Carbon $today, $increase){
    //     /* 
    //         Comparación si después los meses que se excluyo el pago de servicio
    //         son iguales al mes actual
    //     */ 
    //     $dateIncrement = Carbon::parse($assigned)->addMonth($increase);

    //     return ($dateIncrement->isoFormat('YYYY-MM')) > ($today->isoFormat('YYYY-MM')) ? false : true;

    // }

    private function cargoPorPago($service , Contract $contract)
    {$service->createChargeCourtDate($contract);}

    public function extra_charge(ChargeService $service, Contract $contract)
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
 // private function checkInstallation(Contract $contract, Carbon $today, Carbon $endDate, ChargeService $service, ExemptionPeriod $exemption,  $installationSt)
    // {
    //     $equi = new EquipmentChargeDay();
    //     //Verificar si el contrato tiene una instalación asignada
    //     if (!$contract->installations->isEmpty()){

    //         //Iterar cada instalación asignada al contrato
    //         foreach($contract->installations as $installation)
    //         {
    //             $assigned = Carbon::parse($installation->assigned_date);

    //             //Después del 16 -> personalizable
    //             //if($assigned->year >= $today->year){
    //             // BUSCAR SI LA INSTALACION TIENE OTRO MES CONFIGURADO 
                
    //             if(($assigned->day >= $exemption->end_day))
    //             {

    //                 if($installation->installationSettings->exemption_months){
    //                     $exemptionMonth = $installation->installationSettings->exemption_months;
    //                 }else{
    //                     $exemptionMonth = $exemption->month_after_next;
    //                 }
                    
    //                 //Le cobra no el proximo mes, sino el siguiente
    //                 if(self::incomingMonth(
    //                 $assigned, 
    //                 $today, 
    //                 $exemptionMonth, 
    //                 )){
      
    //                     self::conditional($endDate, $contract, $today, $service, $equi);
    //                 }

    //             }else if (($assigned->day >= $exemption->start_day)&&($assigned->day <= $exemption->end_day)){
                    
    //                // $months = $installationSt->getExemptionMonth($installation->id);

    //                 if($installation->installationSettings->exemption_months){
    //                     $exemptionMonth = $installation->installationSettings->exemption_months;
    //                 }else{
    //                     $exemptionMonth = $exemption->month_next;
    //                 }
                    
    //                 //Condicionar si paga el siguiente mes o  el proximo
    //                 if(self::incomingMonth(
    //                 $assigned, 
    //                 $today, 
    //                 $exemptionMonth, 
    //                 ))
    //                 {
    //                     self::conditional($endDate, $contract, $today, $service, $equi);
    //                 }
    //             }else{
    //                 self::conditional($endDate, $contract, $today, $service, $equi);
    //             }
    //            // }
    //         }
    //     }else{
    //         self::conditional($endDate, $contract, $today, $service, $equi);
    //     }
    // }

    // private function conditional(Carbon $endDate, Contract $contract, Carbon $today, ChargeService $service, EquipmentChargeDay $equipmentDay)
    // {
    //     //De general a particular 
    //     if(($endDate->year == $today->year)&&($endDate->month == $today->month))
    //     {

    //         if(($endDate->day) == ($today->day + 2))
    //         {
    //             $this->info("Envio de email en 2 dias");
    //             //Enviar correo
    //             self::sendEmail($contract, "2");

    //         }
    //         else if(($endDate->day) == ($today->day + 7))
    //         {
    //             $this->info("Envio de email en 7 dias");
    //             self::sendEmail($contract, "7");

    //         }else if ($endDate->day == $today->day)
    //         {
    //             // $this->info("Fin del contrato y cargo");
                
    //             //Cortar internet
    //             self::disconectUser($contract);
    //             //Generar cargo

    //             self::Extra_charge($service, $contract);

    //         }else if (!empty(array_filter(get_object_vars($equipmentDay))))
    //         {
                
    //             self::cargoPorPago($service, $contract);
    //         }
    //     }
    //     else
    //     {
    //         if((($endDate->year == $today->year)&&($endDate->month <= $today->month)) && ($endDate->day == $today->day))
    //         {
    //             self::Extra_charge($service, $contract);
    //         }
    //         else if(($endDate->year == $today->year)&&($endDate->month <= $today->month))
    //         {
                
    //             if(!empty(array_filter(get_object_vars($equipmentDay)))){
                    
    //                 self::cargoPorPago($service, $contract);
    //             }
    //         }else if((($endDate->year >= $today->year)&&($endDate->month <= $today->month))&&($endDate->day == $today->day))
    //         {
    //             self::Extra_charge($service, $contract);

    //         }else if(($endDate->year >= $today->year)&&($endDate->month <= $today->month))
    //         {

    //             if(!empty(array_filter(get_object_vars($equipmentDay)))){
                    
    //                 self::cargoPorPago($service, $contract);
    //             }
    //         }
    //     }
    // }