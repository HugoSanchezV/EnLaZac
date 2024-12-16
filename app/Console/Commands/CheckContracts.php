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


        switch($today->day){
            case $equipmentDay:
                $this->processContracts($today, new ChargeService(), $equipmentDay);
            break;

            case '14':
                $this->sanctionContracts();
            break;

            default:
                $this->processContracts($today, new ChargeService());
            break;
        }
    }
    private function sanctionApplied($contract, PaymentSanctionController $controller){
        $controller->applySanction($contract->id);
    }
    /**
     * Verifica si todas las variables necesarias están configuradas
     */
    private function sanctionContracts(){

        $controllerSanction = new PaymentSanctionController();
        $sanctions = $controllerSanction->getSanction();
        if(!is_null($sanctions)){
            
        }
       // $this->info("UNF: ".$sanctions);
        foreach($sanctions as $sanction){
            self::sanctionApplied($sanction->contract, $controllerSanction);
            self::disconectUser($sanction->contract); // Cortar internet
        }
    }

    private function areVariablesConfigured($cutoffDay, $exemption, $equipmentDay): bool
    {
        return $cutoffDay && $exemption && $equipmentDay;
    }

    /**
     * Procesar los contratos según la lógica definida
     */
    private function processContracts(Carbon $today, ChargeService $service, $equipmentDay = null)
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

        if ($endDate->month ==($today->month) && $endDate->year == ($today->year)) {
            // Acciones según el día
          
            if ($endDate->day == $today->day + 2) {
               // $this->info("Envio de email en 2 días");
                self::sendEmail($contract, "2");
            } elseif ($endDate->day == $today->day + 5) {
               // $this->info("Envio de email en 5 días");
                self::sendEmail($contract, "5");

            } elseif ($endDate->day == ($today->day)) {
                self::cargoPorPago($service, $contract); // Cargo adicional

                $this->info("Fin del contrato y cargo con todos el end day actual");
                self::disconectUser($contract); // Cortar internet
                ///self::Extra_charge($service, $contract); // Generar cargo
            } elseif ($today->day == $equipmentDay)  {
                $this->info("Segundo cargo con todos el end day actual");
                self::Extra_charge($service, $contract); // Generar cargo
             //   self::setSanction($contract->id);
            }
        }
        // Verificar si el contrato está en el pasado o en un mes anterior
        elseif ($endDate->isPast()) 
        {  
           // $this->info($equipmentDay);
            if ($endDate->day == ($today->day)) {
                //$this->info("Cargo first");
                self::cargoPorPago($service, $contract); // Cargo adicional

              //  self::Extra_charge($service, $contract);
            } elseif ($today->day == $equipmentDay) {
                //$this->info("Cargo");
                self::Extra_charge($service, $contract); // Generar cargo
            }
        }else{
            $this->info("Conditional entró en else");
        }
    }

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
