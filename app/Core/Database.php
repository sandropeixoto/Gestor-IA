<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
            $this->connection->exec("SET NAMES {$config['charset']} COLLATE {$config['collation']}");
        } catch (PDOException $exception) {
            throw new PDOException('Falha na conexão com o banco de dados: ' . $exception->getMessage(), (int) $exception->getCode());
        }
    }

    public function pdo(): PDO
    {
        return $this->connection;
    }

    public function testConnection(): bool
    {
        return (bool) $this->connection->query('SELECT 1')->fetchColumn();
    }
}
