<?php

namespace App\Exceptions;

use RuntimeException;

class ApiException extends RuntimeException
{
    public function __construct(
        string $mensaje,
        private readonly int $status,
        private readonly string $codigo,
        private readonly array $detalles = [],
    ) {
        parent::__construct($mensaje);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function codigo(): string
    {
        return $this->codigo;
    }

    public function detalles(): array
    {
        return $this->detalles;
    }
}
