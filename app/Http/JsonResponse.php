<?php

declare(strict_types=1);

namespace App\Http;

class JsonResponse
{
    public static function ok(array $data = []): void
    {
        self::send($data, 200);
    }

    public static function created(array $data = []): void
    {
        self::send($data, 201);
    }

    public static function error(string $message, int $code = 400, array $errors = []): void
    {
        $response = ['error' => $message];
        if (!empty($errors)) {
            $response['details'] = $errors;
        }
        self::send($response, $code);
    }

    public static function unauthorized(string $message = 'Não autorizado'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Acesso negado'): void
    {
        self::error($message, 403);
    }

    public static function notFound(string $message = 'Recurso não encontrado'): void
    {
        self::error($message, 404);
    }

    private static function send(array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }
}