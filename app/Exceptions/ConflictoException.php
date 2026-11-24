<?php

namespace App\Exceptions;

class ConflictoException extends ApiException
{
    public function __construct(string $mensaje, string $codigo = 'CONFLICTO_DE_ESTADO', array $detalles = [])
    {
        parent::__construct($mensaje, 409, $codigo, $detalles);
    }
}