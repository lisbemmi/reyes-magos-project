<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoParada;
use App\Http\Controllers\Controller;
use App\Http\Requests\CambiarEstadoParadaRequest;
use App\Http\Requests\ParadaRequest;
use App\Http\Resources\ParadaResource;
use App\Models\Caravana;
use App\Models\Parada;
use App\Services\ParadaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ParadaController extends Controller
{
    public function __construct(private ParadaService $servicio)
    {
    }

    public function index(Caravana $caravana): AnonymousResourceCollection
    {
        return ParadaResource::collection($this->servicio->listar($caravana));
    }

    public function store(ParadaRequest $request, Caravana $caravana): JsonResponse
    {
        $parada = $this->servicio->crear($caravana, $request->validated());

        return (new ParadaResource($parada))->response()->setStatusCode(201);
    }

    public function updateEstado(CambiarEstadoParadaRequest $request, Parada $parada): ParadaResource
    {
        $nuevo = EstadoParada::from($request->validated('estado'));

        return new ParadaResource($this->servicio->cambiarEstado($parada, $nuevo));
    }

    public function destroy(Parada $parada): Response
    {
        $this->servicio->eliminar($parada);

        return response()->noContent();
    }
}