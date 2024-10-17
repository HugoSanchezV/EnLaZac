<?php

namespace App\Console\Commands;

use App\Events\ContractWarningEvent;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ChargeController;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Contract;
use App\Models\Device;
use App\Models\Charge;

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
        $diaT = Carbon::today()->day;
        $mesT = Carbon::today()->month;
        $anoT = Carbon::today()->year;

       // $this->info('DIA DE HOY : '.$diaT);
        
        //END_DATE es igual a la fecha de corte
        $contractTerms = Contract::where('end_date', '<=', $today)->get();
        //INSERTAR CONSULTA DE PAGOS
        $this->info($contractTerms);

        //INSERTAR CONDICION PARA SABER SI EL USUARIO HA PAGADO
        foreach($contractTerms as $contract){
            $endDate = Carbon::parse($contract->end_date);
            if($contract->active == 1)
            {
                if(($endDate->year == $anoT)&&($endDate->month == $mesT))
                {
                   // $this->info('MISMO ANO Y FECHA'.($endDate->day)+2 ." == ".$diaT);

                    if((($endDate->day)+2) == $diaT)
                    {
                      //  $this->info('ENVIADO');
                        //Enviar correo
                        self::sendEmail($contract);

                    }elseif ($endDate->day == $diaT)
                    {
                        
                        //Cortar internet
                       // $this->info('SE VA A DESCONECTAR');
                        self::disconectUser($contract);
                        //Generar cargo

                        self::Extra_charge($contract);
                    }
                }
                if($mesT == 1)
                {
                    if(($diaT == 1)&&(($anoT > $endDate->year)&&($mesT < $endDate->month)))
                    {
                        self::cargoPorPago($contract);
                    }
                }else{
                    if(($diaT == 1)&&(($endDate->year == $anoT)&&($mesT > $endDate->month)))
                    {
                        self::cargoPorPago($contract);
                    }
                }
                
            }

        }
        $this->info('Se han verificado los contratos.');

    }
    public function cargoPorPago($contract)
    {
        $controller = new ChargeController();
        $cargo = new Charge();

        //Set data
        $cargo->contract_id = $contract->id;
        $cargo->description = "No pago el servicio durante el mes";
        $cargo->amount = 50;
        $cargo->paid = false;
        
        //$this->info($cargo);
        $controller->store($cargo);
    }
    public function extra_charge($contract)
    {
        $controller = new ChargeController();
        $cargo = new Charge();

        //Set data
        $cargo->contract_id = $contract->id;
        $cargo->description = "No pagó antes del día de corte";
        $cargo->amount = 50;
        $cargo->paid = false;
        
        //$this->info($cargo);
        $controller->store($cargo);
    }
    public function sendEmail($contract)
    {
        event(new ContractWarningEvent($contract));
    }
    public function disconectUser($contract)
    {
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
        
        $this->info('\n SE TERMINO DE ENVIAR.\n');

            
      
    }
}
