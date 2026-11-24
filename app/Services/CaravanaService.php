<?php

namespace App\Services;

use App\Enums\EstadoCaravana;
use App\Exceptions\ConflictoException;
use App\Models\Caravana;
use Illuminate\Support\Collection;

class CaravanaService
{
    public function listar(?string $estado = null): Collection
    {
        return Caravana::query()
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderBy('id')
            ->get();
    }

    public function crear(array $datos): Caravana
    {
        $this->validarUnicidad($datos);

        // Regla: una caravana nueva siempre nace DETENIDA
        return Caravana::create([...$datos, 'estado' => EstadoCaravana::DETENIDA]);
    }

    public function actualizar(Caravana $caravana, array $datos): Caravana
    {
        // Regla: solo se puede editar en DETENIDA
        if ($caravana->estado !== EstadoCaravana::DETENIDA) {
            throw new ConflictoException(
                "Solo se puede actualizar una caravana DETENIDA (estado actual: {$caravana->estado->value})",
                'CARAVANA_NO_EDITABLE'
            );
        }

        $this->validarUnicidad($datos, $caravana->id);
        $caravana->update($datos);

        return $caravana;
    }

    /** Un rey y una ofrenda solo pueden pertenecer a una caravana. */
    private function validarUnicidad(array $datos, ?int $ignorarId = null): void
    {
        $existe = fn (string $campo) => Caravana::where($campo, $datos[$campo])
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists();

        if ($existe('rey')) {
            throw new ConflictoException("Ya existe una caravana para el rey {$datos['rey']}", 'REY_DUPLICADO');
        }

        if ($existe('ofrenda')) {
            throw new ConflictoException("La ofrenda {$datos['ofrenda']} ya está asignada a otra caravana", 'OFRENDA_DUPLICADA');
        }
    }
}