<?php

namespace App\Enums;

enum EstadoCaravana: string
{
    case DETENIDA = 'DETENIDA';
    case EN_CAMINO = 'EN_CAMINO';
    case EN_BELEN = 'EN_BELEN';
    case OFRENDA_ENTREGADA = 'OFRENDA_ENTREGADA';

    /** @return array<int, self> */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::DETENIDA => [self::EN_CAMINO],
            self::EN_CAMINO => [self::DETENIDA, self::EN_BELEN],
            self::EN_BELEN => [self::OFRENDA_ENTREGADA],
            self::OFRENDA_ENTREGADA => [],
        };
    }

    public function puedePasarA(self $nuevo): bool
    {
        return in_array($nuevo, $this->transicionesPermitidas(), true);
    }
}