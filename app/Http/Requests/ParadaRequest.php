<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ciudad' => ['required', 'string', 'max:100'],
            'orden' => ['required', 'integer', 'min:1'],
            'diasEstancia' => ['required', 'integer', 'between:0,7'],
        ];
    }
}