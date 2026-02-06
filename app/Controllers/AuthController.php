<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;

class AuthController
{
    public function showLogin(array $appConfig, ?string $error = null): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(Auth $auth, array $appConfig): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

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
        $auth->logout();
        Session::start();
        header('Location: /');
        exit;
    }
}
