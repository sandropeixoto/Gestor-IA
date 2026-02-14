<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Models\UserModel;
use App\Models\ReportModel;
use App\Models\ChatLogModel;

class AdminController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function dashboard(array $appConfig, Auth $auth, ReportModel $reports): void
    {
        $this->ensureAdmin($auth);

        // Stats
        $users = $this->userModel->getAllUsers();
        $totalUsers = count($users);
        
        // For MVP, we can just get counts. But ReportModel might not have count methods yet.
        // We can create a simple count query or just list them.
        
        $csrfToken = Csrf::getToken();
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function logs(array $appConfig, Auth $auth, ChatLogModel $chatLogs): void
    {
        $this->ensureAdmin($auth);

        $logs = $chatLogs->getRecentLogs(50); // Need to implement getRecentLogs in ChatLogModel
        
        $csrfToken = Csrf::getToken();
        require __DIR__ . '/../Views/admin/logs/index.php';
    }

    public function index(array $appConfig, Auth $auth): void
    {
        $this->ensureAdmin($auth);

        $users = $this->userModel->getAllUsers();

        $csrfToken = Csrf::getToken();
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require __DIR__ . '/../Views/admin/users/index.php';
    }

    public function edit(array $appConfig, Auth $auth, int $userId): void
    {
        $this->ensureAdmin($auth);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = 'Usuário não encontrado.';
            header('Location: /admin/users');
            exit;
        }

        $managers = $this->userModel->getAllManagers();
        $csrfToken = Csrf::getToken();

        require __DIR__ . '/../Views/admin/users/edit.php';
    }

    public function update(Auth $auth, int $userId): void
    {
        $this->ensureAdmin($auth);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Erro de segurança (CSRF).';
                header("Location: /admin/users/edit/$userId");
                exit;
            }

            $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;

            // Prevent self-assignment
            if ($managerId === $userId) {
                $_SESSION['flash_error'] = 'Um usuário não pode ser seu próprio gestor.';
                header("Location: /admin/users/edit/$userId");
                exit;
            }

            $this->userModel->assignManager($userId, $managerId);
            $_SESSION['flash_success'] = 'Hierarquia atualizada com sucesso!';
            header('Location: /admin/users');
            exit;
        }
    }

    private function ensureAdmin(Auth $auth): void
    {
        $user = $auth->user();
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Acesso negado: Apenas administradores.');
        }
    }
}