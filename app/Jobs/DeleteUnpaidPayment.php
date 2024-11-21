<?php

namespace App\Jobs;

use App\Models\LocalPay;
use App\Models\PagoLocal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnpaidPayment implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pagoLocalId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pagoLocalId)
    {
        $this->pagoLocalId = $pagoLocalId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pagoLocal = LocalPay::find($this->pagoLocalId);

        // Verifica si el pago aún existe y no ha sido pagado
        if ($pagoLocal && $pagoLocal->status !== 'paid') {
            $pagoLocal->delete();
            // Opcional: puedes registrar una actividad o enviar una notificación
        }
    }
}
