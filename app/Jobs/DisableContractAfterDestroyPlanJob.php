<?php

namespace App\Jobs;

use App\Models\Contract;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DisableContractAfterDestroyPlanJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contracts;
    /**
     * Create a new job instance.
     */
    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (is_array($this->contracts) && !empty($this->contracts)) {
                Contract::whereIn('id', $this->contracts)
                    ->update(['active' => 0]);
                Log::info("Se han desactivado los contratos");
            }
        } catch (Exception $e) {
            Log::info("Error al desactivar los contratos " . $e->getMessage());
        }
    }
}
