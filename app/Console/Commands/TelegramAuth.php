<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Iniciar sesión en Telegram usando MadelineProto';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(TelegramService $telegramService)
    {
        $this->info('Iniciando sesión en Telegram...');
        try {
            $telegramService->startSession();
            $this->info('Sesión iniciada exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al iniciar sesión: ' . $e->getMessage());
        }

        return 0;
    }
}
