<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckContracts implements ShouldQueue
{
    use Queueable;


    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = Carbon::today();
        //Verificar por que a partir de las 10 pm, lo toma como el siguiente dia 
        $this->diaT = Carbon::today()->day;
        $this->mesT = Carbon::today()->month;
        $this->anoT = Carbon::today()->year;

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
            if($contract->active == 1)
            {
                if(self::checkInstallation($contract, $today)){
                    self::conditional();
                }
               

                
            }

        }
        $this->info('Se han verificado los contratos.');
    }
    private function conditional($endDate, $contract, $anoT)
    {
        if(($endDate->year == $anoT)&&($endDate->month == $mesT)){

            if((($endDate->day)+2) == $diaT)
            {
                $this->info("Envio de email");
                //Enviar correo
                self::sendEmail($contract);

            }else if ($endDate->day == $diaT)
            {
                $this->info("Fin del contrato y cargo");
                
                //Cortar internet
                self::disconectUser($contract);
                //Generar cargo

                self::Extra_charge($service, $contract);
            }
        }else if($mesT == 1)
        {
            if(($diaT == 1)&&(($anoT > $endDate->year)&&($mesT < $endDate->month)))
            {
                $this->info("Cargo por renta");
                self::cargoPorPago($service, $contract);
            }
        }else{
            if(($diaT == 1)&&(($endDate->year == $anoT)&&($mesT > $endDate->month)))
            {
                $this->info("Cargo por renta");
                self::cargoPorPago($service, $contract);
            }
        }
    }
    private function checkInstallation($contract, $today)
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
                        return self::incomingMonth($assigned, $today, 2, 1, 6);
    
                    }else if (($assigned->day >= 6)&&($assigned->day <= 15)){
                        
                        //Condicionar si paga el siguiente mes o  el proximo
                        return self::incomingMonth($assigned, $today, 1, 1,6);
                     //   return self::incomingMonth($assigned, $today, 2, 1,6);
                    
                        //
                    }else{
                        return true;
                    }
                }else {
                    return false;
                }
            }
        }else{
            return true;
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

    private function extra_charge($service, Contract $contract)
    {$service->createChargeMounthsDebt($contract);}

    private function sendEmail($contract)
    {
        event(new ContractWarningEvent($contract));
    }
    private function disconectUser($contract)
    {
        try{
            $device = Device::where('user_id','=',$contract->user_id)->first();
        
            if($device)
            {
                if($device->disabled == 0)
                {
                    $device->disabled = 1;
                    $controller = new DevicesController();

                    $controller->setDeviceStatusContrato($device);
                }
                
            }
        }catch(Exception $e){
            $this->info($e);
        }
        
        $this->info('\n SE TERMINO DE ENVIAR.\n');
    }
}
