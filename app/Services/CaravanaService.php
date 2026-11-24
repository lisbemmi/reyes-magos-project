<?php

namespace App\Services;

use App\Enums\EstadoCaravana;
use App\Enums\EstadoParada;
use App\Exceptions\ReglaNegocioException;
use App\Exceptions\TransicionInvalidaException;
use App\Enums\Visibilidad;
use App\Integrations\Soap\EstrellaGateway;
use App\Exceptions\ConflictoException;
use App\Models\Caravana;
use Illuminate\Support\Collection;

class CaravanaService
{
    public function __construct(private EstrellaGateway $estrella)
    {
    }

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

    public function cambiarEstado(Caravana $caravana, EstadoCaravana $nuevo): Caravana
    {
        $actual = $caravana->estado;

        // Reglas 1: solo transiciones válidas (409)
        if (! $actual->puedePasarA($nuevo)) {
            throw new TransicionInvalidaException($actual->value, $nuevo->value);
        }

        // Regla 2: para salir hay que consultar la estrella (503 si falla, 422 si está nublada)
        if ($nuevo === EstadoCaravana::EN_CAMINO) {
            $ruta = $this->estrella->consultarRuta($caravana->origen);

            if ($ruta->visibilidad === Visibilidad::NUBLADA) {
                throw new ReglaNegocioException(
                    'La estrella está nublada: la caravana no puede avanzar',
                    'ESTRELLA_NUBLADA',
                    [['diasRestantes' => $ruta->diasRestantes]]
                );
            }
        }

        // Regla 3: para llegar a Belén no puede quedar ninguna parada PLANIFICADA (422)
        if ($nuevo === EstadoCaravana::EN_BELEN) {
            $pendientes = $caravana->paradas()->where('estado', EstadoParada::PLANIFICADA)->count();

            if ($pendientes > 0) {
                throw new ReglaNegocioException(
                    "Hay {$pendientes} parada(s) todavía PLANIFICADA(S)",
                    'PARADAS_PENDIENTES'
                );
            }
        }

        $caravana->update(['estado' => $nuevo]);

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