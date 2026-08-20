<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class DashboardAuthController
 *
 * Handles authentication for the dashboard.
 */
class DashboardAuthController extends Controller
{
    /**
     * Redirects to the login page.
     */
    #[NoReturn]
    public function login(): void
    {
        header('Location: /public/index.php?route=/&login=1');
        exit;
    }

    /**
     * Authenticates the user based on email and password.
     */
    public function authenticate(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'error' => 'Email et mot de passe obligatoires.',
            ]);

            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (
            !$user
            || !password_verify($password, $user['password'])
        ) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'error' => 'Email ou mot de passe incorrect.',
            ]);

            return;
        }

        // Secure the authenticated session.
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        echo json_encode([
            'success' => true,
            'redirect' => '/public/index.php?route=/dashboard',
        ]);
    }

    /**
     * Logs the user out.
     */
    #[NoReturn]
    public function logout(): void
    {
        session_destroy();

        header('Location: /public/index.php?route=/');
        exit;
    }
}