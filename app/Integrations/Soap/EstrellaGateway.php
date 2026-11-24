<?php

namespace App\Integrations\Soap;

interface EstrellaGateway
{
    /** @throws \App\Exceptions\ServicioExternoException si hay Fault, timeout o respuesta ilegible */
    public function consultarRuta(string $origen, string $destino = 'Belen'): RutaEstrella;
}