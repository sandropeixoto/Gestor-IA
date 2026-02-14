<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable |array $handler): void
    {
        $this->add('GET', $uri, $handler);
    }

    public function post(string $uri, callable |array $handler): void
    {
        $this->add('POST', $uri, $handler);
    }

    private function add(string $method, string $uri, callable |array $handler): void
    {
        $this->routes[$method][$uri] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($method);

        if (array_key_exists($uri, $this->routes[$method] ?? [])) {
            $handler = $this->routes[$method][$uri];
            call_user_func($handler);
            return;
        }

        http_response_code(404);
        echo "404 - Página não encontrada";
    }
}