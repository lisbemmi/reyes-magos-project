<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CambiarEstadoCaravanaRequest;
use App\Enums\EstadoCaravana;
use App\Http\Controllers\Controller;
use App\Http\Requests\CaravanaRequest;
use App\Http\Resources\CaravanaResource;
use App\Models\Caravana;
use App\Services\CaravanaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class CaravanaController extends Controller
{
    public function __construct(private CaravanaService $servicio)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'estado' => ['nullable', Rule::enum(EstadoCaravana::class)],
        ]);

        return CaravanaResource::collection($this->servicio->listar($request->query('estado')));
    }

    public function show(Caravana $caravana): CaravanaResource
    {
        return new CaravanaResource($caravana->load('paradas'));
    }

    public function store(CaravanaRequest $request): JsonResponse
    {
        $caravana = $this->servicio->crear($request->validated());

        return (new CaravanaResource($caravana))->response()->setStatusCode(201);
    }

    public function update(CaravanaRequest $request, Caravana $caravana): CaravanaResource
    {
        return new CaravanaResource($this->servicio->actualizar($caravana, $request->validated()));
    }

    public function updateEstado(CambiarEstadoCaravanaRequest $request, Caravana $caravana): CaravanaResource
    {
        $nuevo = EstadoCaravana::from($request->validated('estado'));

        return new CaravanaResource($this->servicio->cambiarEstado($caravana, $nuevo));
    }
}