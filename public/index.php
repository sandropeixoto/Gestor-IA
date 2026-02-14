<?php

declare(strict_types=1)
;

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

// Autoloader para classes da aplicação (App\)
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $className = substr($class, 4);
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

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
    }
    catch (Throwable $throwable) {
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
    }
    catch (Throwable $throwable) {
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
    }
    catch (Throwable $throwable) {
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
    }
    catch (Throwable $throwable) {
        http_response_code(500);
        echo 'Erro de infraestrutura: configure o banco de dados antes de acessar a aplicação.';
        if (($appConfig['debug'] ?? false) === true) {
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
};




$router = new App\Core\Router();

// Web Routes
$router->get('/', function () use ($authController, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) > 0) {
        header('Location: /dashboard');
        exit;
    }
    $authController->showLogin($appConfig);
});

$router->post('/login', function () use ($authController, $authFactory, $appConfig) {
    $authController->login($authFactory(), $appConfig);
});

$router->post('/logout', function () use ($authController, $authFactory) {
    $authController->logout($authFactory());
});

$router->get('/dashboard', function () use ($dashboardController, $authFactory, $reportFactory, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        header('Location: /');
        exit;
    }
    $dashboardController->index($appConfig, $authFactory(), $reportFactory());
});

// Chat Routes
$router->get('/chat', function () use ($chatController, $appConfig, $authFactory, $reportFactory, $chatLogFactory, $evidenceFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        header('Location: /');
        exit;
    }
    $chatController->index($appConfig, $authFactory(), $reportFactory(), $chatLogFactory(), $evidenceFactory());
});

$router->post('/chat/send', function () use ($chatController, $authFactory, $reportFactory, $chatLogFactory, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }
    $llmConfig = $appConfig['llm'] ?? [];
    $chatController->send($authFactory(), $reportFactory(), $chatLogFactory(), new LLMService($llmConfig));
});

$router->post('/chat/upload', function () use ($chatController, $appConfig, $authFactory, $reportFactory, $evidenceFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }
    $chatController->upload($appConfig, $authFactory(), $reportFactory(), $evidenceFactory(), new UploadService());
});
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);