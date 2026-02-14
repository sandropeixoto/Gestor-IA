<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;

class ProfileController
{
    public function index(array $appConfig, Auth $auth, \App\Models\UserInsightModel $insightModel): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        // Fetch user insights (AI Memory)
        $insights = $insightModel->findByUserId((int)$user['id'], 20);

        $csrfToken = Csrf::getToken();
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;

        // Limpa flash messages
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require __DIR__ . '/../Views/profile/index.php';
    }

    public function update(Auth $auth): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Erro de segurança (CSRF). Tente novamente.';
                header('Location: /profile');
                exit;
            }

            $workArea = trim($_POST['work_area'] ?? '');
            $roleDescription = trim($_POST['role_description'] ?? '');

            $validAreas = ['TI', 'Administrativo', 'Financeiro', 'Jurídico', 'RH', 'Obras', 'Geral'];

            if (in_array($workArea, $validAreas, true)) {
                // Atualiza perfil usando o método do Auth que delega para UserModel
                // Precisamos garantir que updateProfile exista no Auth ou chamar direto no UserModel
                // Como Auth já tem updateWorkArea, vamos expandir ou chamar UserModel direto.
                // O ideal é manter a consistência. Vou usar o UserModel injetado no Auth se possível, 
                // mas o Auth::updateWorkArea é restrito. 
                // Vou adicionar updateProfile no Auth também para manter o padrão.

                $auth->updateProfile((int)$user['id'], $workArea, $roleDescription);
                $_SESSION['flash_success'] = 'Perfil atualizado com sucesso!';
            }
            else {
                $_SESSION['flash_error'] = 'Área de atuação inválida.';
            }

            header('Location: /profile');
            exit;
        }
    }
    public function assignManagerByEmail(Auth $auth): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Erro de segurança (CSRF). Tente novamente.';
                header('Location: /profile');
                exit;
            }

            $email = trim($_POST['manager_email'] ?? '');

            if (empty($email)) {
                // If email is empty, maybe they want to clear it? For now let's just error if empty.
                $_SESSION['flash_error'] = 'Por favor, informe o email do gestor.';
                header('Location: /profile');
                exit;
            }

            if ($email === $user['email']) {
                $_SESSION['flash_error'] = 'Você não pode ser seu próprio gestor.';
                header('Location: /profile');
                exit;
            }

            $userModel = $auth->userModel();
            $manager = $userModel->findByEmail($email);

            if ($manager) {
                // Found manager
                $userModel->assignManager((int)$user['id'], (int)$manager['id']);
                $_SESSION['flash_success'] = 'Gestor atribuído com sucesso!';
            }
            else {
                // Manager not found
                $_SESSION['flash_error'] = 'Tente novamente um novo email ou aguarde um período até que o gestor se cadastre.';
            }

            header('Location: /profile');
            exit;
        }
    }
}