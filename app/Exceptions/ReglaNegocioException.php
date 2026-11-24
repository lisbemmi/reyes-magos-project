<?php

namespace App\Exceptions;

class ReglaNegocioException extends ApiException
{
    public function __construct(string $mensaje, string $codigo = 'REGLA_DE_NEGOCIO', array $detalles = [])
    {
        parent::__construct($mensaje, 422, $codigo, $detalles);
    }
}