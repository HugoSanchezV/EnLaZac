<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateTelegramUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $userId;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $message = "Tus datos han sido actualizados")
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::findOrFail($this->userId);

            $telegramService = app(TelegramService::class);

            $data = [
                'phone' => $user->phone,
                'name' => $user->name,
                'alias' => $user->alias,
            ];

            UserTelegramService::createContactTelegramSendMessage($data, $telegramService, $this->message);

            Log::info("Se ha actualizado el usuario con ID: {$this->userId}");
        } catch (Exception $e) {
            Log::error("Error al actualizar el usuario de Telegram: " . $e->getMessage());
        }
    }
}
