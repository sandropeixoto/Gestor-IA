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

// Autoloader para classes da aplicação (App\)
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $className = substr($class, 4);
        $file = __DIR__ . '/app/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

Env::load(__DIR__ . '/.env);
Session::start();

$appConfig = require __DIR__ . '/config/app.php';
$dbConfig = require __DIR__ . '/config/database.php';

// Ajuste para rodar em subpasta (ex: /gestor-ia)
$basePath = parse_url($appConfig['url'] ?? '', PHP_URL_PATH) ?: '';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET);

$authController = new AuthController();
$dashboardController = new DashboardController();
$profileController = new \App\Controllers\ProfileController();
$chatController = new ChatController();
$reportController = new \App\Controllers\ReportController();
$teamController = new \App\Controllers\TeamController();
$notificationController = new \App\Controllers\NotificationController();

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
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8);
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
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8);
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
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8);
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
            echo '<br><br>Detalhe: ' . htmlspecialchars($throwable->getMessage(), ENT_QUOTES, 'UTF-8);
        }
        exit;
    }
};

$userInsightFactory = static function () use ($dbConfig, $appConfig): \App\Models\UserInsightModel {
    try {
        $database = new Database($dbConfig);
        return new \App\Models\UserInsightModel($database->pdo());
    }
    catch (Throwable $throwable) {
        http_response_code(500);
        exit;
    }
};

$notificationFactory = static function () use ($dbConfig, $appConfig): \App\Models\NotificationModel {
    try {
        $database = new Database($dbConfig);
        return new \App\Models\NotificationModel($database->pdo());
    }
    catch (Throwable $throwable) {
        http_response_code(500);
        exit;
    }
};

$deadlineFactory = static function () use ($dbConfig, $appConfig): \App\Models\DeadlineModel {
    try {
        $database = new Database($dbConfig);
        return new \App\Models\DeadlineModel($database->pdo());
    }
    catch (Throwable $throwable) {
        http_response_code(500);
        exit;
    }
};

$aiPersonaFactory = static function () use ($dbConfig, $appConfig): \App\Models\AiPersonaModel {
    try {
        $database = new Database($dbConfig);
        return new \App\Models\AiPersonaModel($database->pdo());
    }
    catch (Throwable $throwable) {
        http_response_code(500);
        exit;
    }
};

$router = new App\Core\Router();

// Web Routes
$router->get('/', function () use ($authController, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) > 0) {
        App\Core\Router::redirect('/dashboard);
        exit;
    }
    $authController->showLogin($appConfig);
});

$router->post('/login', function () use ($authController, $authFactory, $appConfig) {
    $authController->login($authFactory(), $appConfig);
});

$router->get('/auth/sso', function () use ($authController, $authFactory, $appConfig) {
    $authController->sso($authFactory(), $appConfig);
});

// Alias para sistemas que chamam o arquivo diretamente
$router->get('/auth_sso.php', function () use ($authController, $authFactory, $appConfig) {
    $authController->sso($authFactory(), $appConfig);
});

$router->post('/logout', function () use ($authController, $authFactory) {
    $authController->logout($authFactory());
});

$router->get('/dashboard', function () use ($dashboardController, $authFactory, $reportFactory, $appConfig, $deadlineFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $dashboardController->index($appConfig, $authFactory(), $reportFactory(), $deadlineFactory());
});

$router->post('/dashboard/update-profile', function () use ($dashboardController, $authFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $dashboardController->updateWorkArea($authFactory());
});

$router->get('/profile', function () use ($profileController, $authFactory, $appConfig, $userInsightFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    // Inject UserInsightModel to display learned facts
    $profileController->index($appConfig, $authFactory(), $userInsightFactory());
});

$router->post('/profile/update', function () use ($profileController, $authFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $profileController->update($authFactory());
});

$router->post('/profile/assign-manager', function () use ($profileController, $authFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $profileController->assignManagerByEmail($authFactory());
});



// Admin Routes
$adminController = new \App\Controllers\AdminController($authFactory()->userModel()); // Need to expose userModel getter or use dependency injection better

// Since authFactory returns Auth which has userModel protected/private, let's instantiate AdminController differently
// Or better, let's just instantiate it here like the others
$adminController = new \App\Controllers\AdminController(new \App\Models\UserModel((new \App\Core\Database($dbConfig))->pdo()));

$router->get('/admin', function () use ($adminController, $authFactory, $appConfig, $reportFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->dashboard($appConfig, $authFactory(), $reportFactory());
});

$router->get('/admin/logs', function () use ($adminController, $authFactory, $appConfig, $chatLogFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->logs($appConfig, $authFactory(), $chatLogFactory());
});

$router->get('/admin/users', function () use ($adminController, $authFactory, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->index($appConfig, $authFactory());
});

$router->get('/admin/users/edit/(\d+)', function ($userId) use ($adminController, $authFactory, $appConfig) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->edit($appConfig, $authFactory(), (int)$userId);
});

$router->post('/admin/users/update/(\d+)', function ($userId) use ($adminController, $authFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->update($authFactory(), (int)$userId);
});

// Chat Routes
$router->get('/chat', function () use ($chatController, $appConfig, $authFactory, $reportFactory, $chatLogFactory, $evidenceFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $chatController->index($appConfig, $authFactory(), $reportFactory(), $chatLogFactory(), $evidenceFactory());
});

$router->post('/chat/send', function () use ($chatController, $authFactory, $reportFactory, $chatLogFactory, $userInsightFactory, $appConfig, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }
    $llmConfig = $appConfig['llm'] ?? [];
    $chatController->send($authFactory(), $reportFactory(), $chatLogFactory(), new LLMService($llmConfig), $userInsightFactory(), $aiPersonaFactory());
});

$router->post('/chat/save-draft', function () use ($chatController, $authFactory, $reportFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        exit;
    }
    $chatController->saveDraft($authFactory(), $reportFactory());
});

// Admin Persona Routes
$router->get('/admin/personas', function () use ($adminController, $authFactory, $appConfig, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->personas($appConfig, $authFactory(), $aiPersonaFactory());
});

$router->post('/admin/personas/store', function () use ($adminController, $authFactory, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->storePersona($authFactory(), $aiPersonaFactory());
});

$router->get('/admin/personas/edit/(\d+)', function ($id) use ($adminController, $authFactory, $appConfig, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->editPersona($appConfig, $authFactory(), $aiPersonaFactory(), (int)$id);
});

$router->post('/admin/personas/update/(\d+)', function ($id) use ($adminController, $authFactory, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->updatePersona($authFactory(), $aiPersonaFactory(), (int)$id);
});

$router->post('/admin/personas/delete/(\d+)', function ($id) use ($adminController, $authFactory, $aiPersonaFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $adminController->deletePersona($authFactory(), $aiPersonaFactory(), (int)$id);
});

$router->post('/chat/upload', function () use ($chatController, $appConfig, $authFactory, $reportFactory, $evidenceFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }
    $chatController->upload($appConfig, $authFactory(), $reportFactory(), $evidenceFactory(), new UploadService());
});

$router->post('/chat/submit', function () use ($chatController, $appConfig, $authFactory, $reportFactory, $userInsightFactory, $notificationFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit;
    }
    $llmConfig = $appConfig['llm'] ?? [];
    $chatController->submit($authFactory(), $reportFactory(), new LLMService($llmConfig), $userInsightFactory(), $notificationFactory());
});

// Report Routes
$router->get('/reports', function () use ($reportController, $appConfig, $authFactory, $reportFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $reportController->index($appConfig, $authFactory(), $reportFactory());
});

$router->get('/reports/view/(\d+)', function ($reportId) use ($reportController, $appConfig, $authFactory, $reportFactory, $chatLogFactory, $evidenceFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $reportController->view((int)$reportId, $appConfig, $authFactory(), $reportFactory(), $chatLogFactory(), $evidenceFactory());
});

$router->post('/reports/review/(\d+)', function ($reportId) use ($reportController, $authFactory, $reportFactory, $notificationFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $reportController->processReview((int)$reportId, $authFactory(), $reportFactory(), $notificationFactory());
});

// Team Routes
$router->get('/team', function () use ($teamController, $appConfig, $authFactory, $deadlineFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $teamController->index($appConfig, $authFactory(), $authFactory()->userModel(), $deadlineFactory());
});

$router->post('/team/deadline', function () use ($teamController, $authFactory, $deadlineFactory, $notificationFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $teamController->updateDeadline($authFactory(), $deadlineFactory(), $notificationFactory(), $authFactory()->userModel());
});

$router->get('/team/insights', function () use ($teamController, $appConfig, $authFactory, $userInsightFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $teamController->insights($appConfig, $authFactory(), $userInsightFactory());
});

$router->get('/team/user/(\d+)', function ($userId) use ($teamController, $appConfig, $authFactory, $reportFactory, $userInsightFactory) {
    if ((int)Session::get('auth_user_id', 0) <= 0) {
        App\Core\Router::redirect('/);
        exit;
    }
    $teamController->viewUser((int)$userId, $appConfig, $authFactory(), $authFactory()->userModel(), $reportFactory(), $userInsightFactory());
});

// Notifications API
$router->get('/api/notifications', function () use ($notificationController, $authFactory, $notificationFactory) {
    $notificationController->list($authFactory(), $notificationFactory());
});

$router->post('/api/notifications/read/(\d+)', function ($id) use ($notificationController, $authFactory, $notificationFactory) {
    $notificationController->markRead((int)$id, $authFactory(), $notificationFactory());
});

$router->dispatch($path, $method);