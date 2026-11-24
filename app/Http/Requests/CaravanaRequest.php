<?php

namespace App\Http\Requests;

use App\Enums\Ofrenda;
use App\Enums\Rey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaravanaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la autorización la hace el middleware de rol
    }

    public function rules(): array
    {
        return [
            'rey' => ['required', Rule::enum(Rey::class)],
            'ofrenda' => ['required', Rule::enum(Ofrenda::class)],
            'origen' => ['required', 'string', 'max:100'],
            'camellos' => ['required', 'integer', 'between:1,12'],
        ];
    }
}