<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParadaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'caravanaId' => $this->caravana_id,
            'ciudad' => $this->ciudad,
            'orden' => $this->orden,
            'diasEstancia' => $this->dias_estancia,
            'estado' => $this->estado->value,
        ];
    }
}