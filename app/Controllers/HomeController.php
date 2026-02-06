<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;

class HomeController
{
    public function index(array $appConfig, array $dbConfig): void
    {
        $dbStatus = 'Não testado';

        try {
            $database = new Database($dbConfig);
            $dbStatus = $database->testConnection() ? 'Conexão com MySQL OK' : 'Sem resposta do MySQL';
        } catch (\Throwable $throwable) {
            $dbStatus = 'Erro: ' . $throwable->getMessage();
        }

        require __DIR__ . '/../Views/home.php';
    }
}
