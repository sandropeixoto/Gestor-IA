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

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Converte a rota em regex se ela não for uma correspondência exata
            $pattern = "#^" . $route . "$#";
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove a correspondência completa
                
                if (is_array($handler)) {
                    [$controller, $action] = $handler;
                    $controllerInstance = new $controller();
                    $controllerInstance->$action(...$matches);
                } else {
                    call_user_func_array($handler, $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 - Página não encontrada";
    }
}