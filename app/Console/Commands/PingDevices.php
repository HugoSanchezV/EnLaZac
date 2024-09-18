<?php

namespace App\Console\Commands;

use App\Models\Device;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PingDevices extends Command
{
    protected $signature = 'app:ping-devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping a todos los dispositivos cada hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $device = Device::select('id','address') ->get();

        foreach($device as $ip)
        {
            $this->info("");
            self::pingDevice($ip);
        }
    }
    private function pingDevice($ip)
    {
        // Ejecutar el comando ping
        $process = new Process(['ping', '-c', '4', $ip->address]);
        $process->run();

        // Verificar si el comando se ejecutó correctamente
        if (!$process->isSuccessful()) {
            $this->error("Failed to ping: $ip");
        } else {
            $this->info("Ping successful for: $ip");
            $this->info($process->getOutput());
        }
    }
}
