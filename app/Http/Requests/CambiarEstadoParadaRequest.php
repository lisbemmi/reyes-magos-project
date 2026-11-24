<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CambiarEstadoParadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Solo se puede marcar como cumplida o cancelada
            'estado' => ['required', Rule::in(['CUMPLIDA', 'CANCELADA'])],
        ];
    }
}