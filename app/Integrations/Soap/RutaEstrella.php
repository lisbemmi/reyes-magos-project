<?php

namespace App\Integrations\Soap;

use App\Enums\Visibilidad;

final class RutaEstrella
{
    public function __construct(
        public readonly Visibilidad $visibilidad,
        public readonly int $diasRestantes,
    ) {
    }
}