<?php

require __DIR__ . '/../app/Core/Env.php';
use App\Core\Env;

// Load .env file manually
Env::load(__DIR__ . '/../.env');

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: 3306;
$dbName = getenv('DB_DATABASE') ?: 'gestor_ia';
$dbUser = getenv('DB_USERNAME') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $sql = file_get_contents(__DIR__ . '/../database/update_v3.sql');
    $pdo->exec($sql);
    echo "Migration applied successfully.\n";
}
catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}