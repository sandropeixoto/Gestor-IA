<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ChatController;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Env;
use App\Core\Session;
use App\Models\ChatLogModel;
use App\Models\EvidenceModel;
use App\Models\ReportModel;
use App\Models\UserModel;
use App\Services\LLMService;
use App\Services\UploadService;

require_once __DIR__ . '/../app/Core/Env.php';
require_once __DIR__ . '/../app/Core/Session.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Models/UserModel.php';
require_once __DIR__ . '/../app/Models/ReportModel.php';
require_once __DIR__ . '/../app/Models/ChatLogModel.php';
require_once __DIR__ . '/../app/Models/EvidenceModel.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/ChatController.php';
require_once __DIR__ . '/../app/Services/LLMService.php';
require_once __DIR__ . '/../app/Services/UploadService.php';

Env::load(__DIR__ . '/../.env');
Session::start();

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$authController = new AuthController();
$dashboardController = new DashboardController();
$chatController = new ChatController();

$authFactory = static function () use ($dbConfig, $appConfig): Auth {
    try {
        $database = new Database($dbConfig);
        $userModel = new UserModel($database->pdo());

        return new Auth($userModel);
    } catch (Throwable $throwable) {
        http_response_code(500);
        echo 'Erro de infraestrutura: configure o banco de dados antes de acessar a aplicação.';
        if (($appConfig['debug'] ?? false) === true) {
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
};

$reportFactory = static function () use ($dbConfig, $appConfig): ReportModel {
    try {
        $database = new Database($dbConfig);

        return new ReportModel($database->pdo());
    } catch (Throwable $throwable) {
        http_response_code(500);
        echo 'Erro de infraestrutura: configure o banco de dados antes de acessar a aplicação.';
        if (($appConfig['debug'] ?? false) === true) {
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
};

$chatLogFactory = static function () use ($dbConfig, $appConfig): ChatLogModel {
    try {
        $database = new Database($dbConfig);

        return new ChatLogModel($database->pdo());
    } catch (Throwable $throwable) {
        http_response_code(500);
        echo 'Erro de infraestrutura: configure o banco de dados antes de acessar a aplicação.';
        if (($appConfig['debug'] ?? false) === true) {
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
};

$evidenceFactory = static function () use ($dbConfig, $appConfig): EvidenceModel {
    try {
        $database = new Database($dbConfig);

        return new EvidenceModel($database->pdo());
    } catch (Throwable $throwable) {
        http_response_code(500);
        echo 'Erro de infraestrutura: configure o banco de dados antes de acessar a aplicação.';
        if (($appConfig['debug'] ?? false) === true) {
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
};




if ($path === '/' && $method === 'GET') {
    $hasSessionUser = (int) Session::get('auth_user_id', 0) > 0;
    if ($hasSessionUser) {
        header('Location: /dashboard');
        exit;
    }

    $authController->showLogin($appConfig);
    exit;
}

if ($path === '/login' && $method === 'POST') {
    $authController->login($authFactory(), $appConfig);
    exit;
}

if ($path === '/logout' && $method === 'POST') {
    $authController->logout($authFactory());
    exit;
}

if ($path === '/dashboard' && $method === 'GET') {
    if ((int) Session::get('auth_user_id', 0) <= 0) {
        header('Location: /');
        exit;
    }

    $dashboardController->index($appConfig, $authFactory(), $reportFactory());
    exit;
}

if ($path === '/chat' && $method === 'GET') {
    if ((int) Session::get('auth_user_id', 0) <= 0) {
        header('Location: /');
        exit;
    }

    $chatController->index($appConfig, $authFactory(), $reportFactory(), $chatLogFactory(), $evidenceFactory());
    exit;
}

if ($path === '/chat/send' && $method === 'POST') {
    if ((int) Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }

    $chatController->send($authFactory(), $reportFactory(), $chatLogFactory(), new LLMService());
    exit;
}


if ($path === '/chat/upload' && $method === 'POST') {
    if ((int) Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }

    $chatController->upload($appConfig, $authFactory(), $reportFactory(), $evidenceFactory(), new UploadService());
    exit;
}

http_response_code(404);
echo 'Página não encontrada';
