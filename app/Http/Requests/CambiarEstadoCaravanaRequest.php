<?php

namespace App\Http\Requests;

use App\Enums\EstadoCaravana;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CambiarEstadoCaravanaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::enum(EstadoCaravana::class)],
        ];
    }
}