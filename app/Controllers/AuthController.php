<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\Csrf;

class AuthController
{
    public function showLogin(array $appConfig, ?string $error = null): void
    {
        $csrfToken = Csrf::getToken();
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(Auth $auth, array $appConfig): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $this->showLogin($appConfig, 'Sessão inválida (CSRF). Tente novamente.');
            return;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->showLogin($appConfig, 'Preencha e-mail e senha.');
            return;
        }

        if (!$auth->attempt($email, $password)) {
            $this->showLogin($appConfig, 'Credenciais inválidas.');
            return;
        }

        header('Location: /dashboard');
        exit;
    }

    public function logout(Auth $auth): void
    {
        // Logout via POST requires CSRF
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF invalido no logout.');
        }

        $auth->logout();
        Session::start();
        header('Location: /');
        exit;
    }
}