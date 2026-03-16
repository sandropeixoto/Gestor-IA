<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\Csrf;
use App\Core\Router;

class AuthController
{
    public function showLogin(array $appConfig, ?string $error = null): void
    {
        $csrfToken = Csrf::getToken();
        require __DIR__ . '/app/Views/auth/login.php';
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

        Router::redirect('/dashboard');
        exit;
    }

    public function sso(Auth $auth, array $appConfig): void
    {
        $payloadBase64 = $_GET['sso_payload'] ?? null;
        $signature = $_GET['sso_sig'] ?? null;

        if (!$payloadBase64 || !$signature) {
            $this->showLogin($appConfig, 'Acesso negado: Token SSO ausente.');
            return;
        }

        if (!$auth->loginViaSso($payloadBase64, $signature)) {
            $this->showLogin($appConfig, 'Acesso negado: Assinatura inválida ou token expirado.');
            return;
        }

        Router::redirect('/dashboard');
        exit;
    }

    public function logout(Auth $auth): void
    {
        // Logout via POST requires CSRF
        // Se o CSRF falhar (sessão expirada), apenas redirecionamos para o login
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Router::redirect('/');
            exit;
        }

        $auth->logout();
        Session::start();
        Router::redirect('/');
        exit;
    }
}