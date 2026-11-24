<?php

namespace App\Enums;

enum EstadoParada: string
{
    case PLANIFICADA = 'PLANIFICADA';
    case CUMPLIDA = 'CUMPLIDA';
    case CANCELADA = 'CANCELADA';
}