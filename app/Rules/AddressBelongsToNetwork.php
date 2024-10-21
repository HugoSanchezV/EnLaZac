<?php

namespace App\Rules;

use App\Models\Network;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressBelongsToNetwork implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $routerId;

    public function __construct($routerId)
    {
        $this->routerId = $routerId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    public function passes($attribute, $value)
    {
        // Obtener las redes del router
        $networks = Network::where('router_id', $this->routerId)->get();

        foreach ($networks as $network) {
            // Comparar los primeros tres octetos de la dirección IP con la red
            if ($this->matchesNetwork($network->network, $value)) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'La dirección no pertenece a ninguna de las redes del router.';
    }

    /**
     * Verifica si el `address` pertenece a la red `network`.
     */
    protected function matchesNetwork($network, $address)
    {
        // Extraemos los primeros tres octetos (XXX.XXX.XXX) tanto de la red como del address
        $networkParts = implode('.', array_slice(explode('.', $network), 0, 3));
        $addressParts = implode('.', array_slice(explode('.', $address), 0, 3));

        return $networkParts === $addressParts;
    }
}
