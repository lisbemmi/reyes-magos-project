<?php

namespace App\Services;

use App\Enums\EstadoCaravana;
use App\Enums\EstadoParada;
use App\Exceptions\ConflictoException;
use App\Exceptions\TransicionInvalidaException;
use App\Models\Caravana;
use App\Models\Parada;
use Illuminate\Support\Collection;

class ParadaService
{
    public function listar(Caravana $caravana): Collection
    {
        return $caravana->paradas()->get(); // la relación ya viene ordenada por "orden"
    }

    public function crear(Caravana $caravana, array $datos): Parada
    {
        // Regla 5: no se agregan paradas si la caravana ya llegó a Belén
        if (in_array($caravana->estado, [EstadoCaravana::EN_BELEN, EstadoCaravana::OFRENDA_ENTREGADA], true)) {
            throw new ConflictoException(
                "No se pueden agregar paradas a una caravana en {$caravana->estado->value}",
                'CARAVANA_CERRADA'
            );
        }

        // El orden no se repite dentro de la misma caravana
        if ($caravana->paradas()->where('orden', $datos['orden'])->exists()) {
            throw new ConflictoException(
                "Ya existe una parada con orden {$datos['orden']} en esta caravana",
                'ORDEN_DUPLICADO'
            );
        }

        return $caravana->paradas()->create([
            'ciudad' => $datos['ciudad'],
            'orden' => $datos['orden'],
            'dias_estancia' => $datos['diasEstancia'],
            'estado' => EstadoParada::PLANIFICADA,
        ]);
    }

    public function cambiarEstado(Parada $parada, EstadoParada $nuevo): Parada
    {
        // Solo una parada PLANIFICADA puede cambiar de estado
        if ($parada->estado !== EstadoParada::PLANIFICADA) {
            throw new TransicionInvalidaException($parada->estado->value, $nuevo->value);
        }

        // Regla 4: las paradas se cumplen en orden
        if ($nuevo === EstadoParada::CUMPLIDA && $this->hayAnteriorPlanificada($parada)) {
            throw new ConflictoException(
                'No se puede cumplir esta parada: hay una parada anterior todavía PLANIFICADA',
                'ORDEN_INCUMPLIDO'
            );
        }

        $parada->update(['estado' => $nuevo]);

        return $parada;
    }

    public function eliminar(Parada $parada): void
    {
        if ($parada->estado !== EstadoParada::PLANIFICADA) {
            throw new ConflictoException(
                "Solo se pueden eliminar paradas PLANIFICADAS (estado actual: {$parada->estado->value})",
                'PARADA_NO_ELIMINABLE'
            );
        }

        $parada->delete();
    }

    private function hayAnteriorPlanificada(Parada $parada): bool
    {
        return Parada::where('caravana_id', $parada->caravana_id)
            ->where('orden', '<', $parada->orden)
            ->where('estado', EstadoParada::PLANIFICADA)
            ->exists();
    }
}