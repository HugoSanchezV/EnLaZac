<?php

namespace App\Console\Commands;

use App\Events\ContractWarningEvent;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ContractController;
use App\Services\ChargeService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Contract;
use App\Models\Device;
use App\Models\Charge;
use App\Models\TelegramAccount;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Illuminate\Support\Facades\Log;
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
        //Verificar por que a partir de las 10 pm, lo toma como el siguiente dia 

        $controllerContract = new ContractController();

       // $this->info('DIA DE HOY : '.$diaT);
        
        //END_DATE es igual a la fecha de corte
        $service = new ChargeService();

        
        $contractTerms = $controllerContract->getContracts($today);

        //INSERTAR CONSULTA DE PAGOS
        $this->info($contractTerms);

        //INSERTAR CONDICION PARA SABER SI EL USUARIO HA PAGADO

        //Configurar fecha de corte jaajaja
        // if($diaT == 6){

        // }
        foreach($contractTerms as $contract){
            $endDate = Carbon::parse($contract->end_date);
            if($contract->active == 1){
                self::checkInstallation($contract, $today, $endDate, $service);
            }
        }
        $this->info('Se han verificado los contratos.');
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
    private function checkInstallation($contract, $today, $endDate, $service)
    {
        if (!$contract->installations->isEmpty()){

            foreach($contract->installations as $installation)
            {
                $assigned = Carbon::parse($installation->assigned_date);
                //Después del 16 -> personalizable
                if($assigned->year >= $today->year){

                    if(($assigned->day >= 16) && ($assigned->day < 32))
                    {
                        //Le cobra no el proximo mes, sino el siguiente
                        if(self::incomingMonth($assigned, $today, 2, 1, 6)){
                            self::conditional($endDate, $contract, $today, $service);
                        }
    
                    }else if (($assigned->day >= 6)&&($assigned->day <= 15)){
                        //Condicionar si paga el siguiente mes o  el proximo
                        if(self::incomingMonth($assigned, $today, 1, 1,6))
                        {
                             self::conditional($endDate, $contract, $today, $service);
                        }
                    }else{
                        self::conditional($endDate, $contract, $today, $service);
                    }
                }
            }
        }else{
            self::conditional($endDate, $contract, $today, $service);
        }
    }
    private function incomingMonth($assigned, $today, $increase, $start, $end){
        if($assigned->month+$increase == $today->month)
        {
            if(($today->day >= $start) && ($today->day < $end))
            {
                return true;
            }
        }else if($assigned->month < $today->month || $assigned->year < $today->year){
            if(($today->day >= $start) && ($today->day < $end))
            {
                return true;
            }
        }
        return false;
    }
    private function cargoPorPago($service , Contract $contract)
    {$service->createChargeCourtDate($contract);}

    public function extra_charge($service, Contract $contract)
    {
        $service->createChargeMounthsDebt($contract);
    }

    private function sendEmail($contract, $days)
    {
        event(new ContractWarningEvent($contract, $days));
    }
    private function disconectUser($contract)
    {
        try{
            $devices = Device::findOrFail($contract->device_id);
            
            if($devices)
            {
                foreach($devices  as $device)
                {
                    if($device->disabled == 0)
                    {
                        $device->disabled = 1;
                        $controller = new DevicesController();
    
                        $controller->setDeviceStatusContrato($device);
                    }
                }
                
            }
        }catch(Exception $e){
            throw new Exception('Error' . $e->getMessage());
        }
        
        $this->info('\n SE TERMINO DE ENVIAR.\n');
    }
}
