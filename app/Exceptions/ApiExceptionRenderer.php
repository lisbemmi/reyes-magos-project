<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionRenderer
{
    public static function render(Throwable $e, Request $request): JsonResponse
    {
        [$status, $codigo, $mensaje, $detalles] = self::mapear($e);

        return response()->json([
            'codigo' => $codigo,
            'mensaje' => $mensaje,
            'detalles' => $detalles,
            'timestamp' => now('UTC')->format('Y-m-d\TH:i:s\Z'),
            'ruta' => '/'.ltrim($request->path(), '/'),
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }

    private static function mapear(Throwable $e): array
    {
        return match (true) {
            $e instanceof ApiException => [$e->status(), $e->codigo(), $e->getMessage(), $e->detalles()],
            $e instanceof ValidationException => [400, 'VALIDACION_FALLIDA', 'Los datos enviados no son válidos', self::detallesValidacion($e)],
            $e instanceof AuthenticationException => [401, 'NO_AUTENTICADO', 'Token ausente o inválido', []],
            $e instanceof HttpExceptionInterface => self::mapearHttp($e),
            default => [500, 'ERROR_INTERNO', config('app.debug') ? $e->getMessage() : 'Error interno del servidor', []],
        };
    }

    private static function mapearHttp(HttpExceptionInterface $e): array
    {
        return match ($e->getStatusCode()) {
            403 => [403, 'PROHIBIDO', 'No tienes permisos para realizar esta acción', []],
            404 => [404, 'NO_ENCONTRADO', 'El recurso solicitado no existe', []],
            405 => [405, 'METODO_NO_PERMITIDO', 'Método HTTP no permitido para esta ruta', []],
            429 => [429, 'DEMASIADAS_SOLICITUDES', 'Demasiadas solicitudes', []],
            default => [$e->getStatusCode(), 'ERROR_HTTP', $e->getMessage() ?: 'Error en la solicitud', []],
        };
    }

    private static function detallesValidacion(ValidationException $e): array
    {
        $detalles = [];
        foreach ($e->errors() as $campo => $mensajes) {
            foreach ($mensajes as $mensaje) {
                $detalles[] = ['campo' => $campo, 'mensaje' => $mensaje];
            }
        }

        return $detalles;
    }
}