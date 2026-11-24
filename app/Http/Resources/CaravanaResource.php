<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaravanaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rey' => $this->rey->value,
            'ofrenda' => $this->ofrenda->value,
            'origen' => $this->origen,
            'camellos' => $this->camellos,
            'estado' => $this->estado->value,
            'paradas' => ParadaResource::collection($this->whenLoaded('paradas')),
        ];
    }
}