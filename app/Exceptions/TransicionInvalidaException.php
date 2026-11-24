<?php

namespace App\Exceptions;

class TransicionInvalidaException extends ApiException
{
    public function __construct(string $desde, string $hacia)
    {
        parent::__construct("No se puede pasar de {$desde} a {$hacia}", 409, 'TRANSICION_INVALIDA');
    }
}