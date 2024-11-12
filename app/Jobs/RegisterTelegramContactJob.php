<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; // Importar Dispatchable
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegisterTelegramContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(TelegramService $telegramService)
    {
        try {
            $chatId = UserTelegramService::createContactTelegramSendMessage([
                'name' => $this->user->name,
                'alias' => $this->user->alias ?? '',
                'phone' => '52' . $this->user->phone,
            ], $telegramService);

            if ($chatId) {
                $this->user->telegramAccount()->create(['chat_id' => $chatId]);
            }
        } catch (\Exception $e) {
            Log::info('Error al registrar usuario en telegram: ' . $e->getMessage());
        }
    }
}
