<?php

namespace NovaCore\Http;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
