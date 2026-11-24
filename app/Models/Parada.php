<?php

namespace App\Models;

use App\Enums\EstadoParada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parada extends Model
{
    protected $fillable = ['caravana_id', 'ciudad', 'orden', 'dias_estancia', 'estado'];

    protected function casts(): array
    {
        return [
            'estado' => EstadoParada::class,
            'orden' => 'integer',
            'dias_estancia' => 'integer',
        ];
    }

    public function caravana(): BelongsTo
    {
        return $this->belongsTo(Caravana::class);
    }
}