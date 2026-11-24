<?php

namespace App\Models;

use App\Enums\EstadoCaravana;
use App\Enums\Ofrenda;
use App\Enums\Rey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caravana extends Model
{
    protected $fillable = ['rey', 'ofrenda', 'origen', 'camellos', 'estado'];

    protected function casts(): array
    {
        return [
            'rey' => Rey::class,
            'ofrenda' => Ofrenda::class,
            'estado' => EstadoCaravana::class,
            'camellos' => 'integer',
        ];
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(Parada::class)->orderBy('orden');
    }
}