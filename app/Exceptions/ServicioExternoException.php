<?php

namespace App\Exceptions;

class ServicioExternoException extends ApiException
{
    public function __construct(string $mensaje = 'El servicio de la Estrella de Belén no está disponible', array $detalles = [])
    {
        parent::__construct($mensaje, 503, 'SERVICIO_EXTERNO_NO_DISPONIBLE', $detalles);
    }
}