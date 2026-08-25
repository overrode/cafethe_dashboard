<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;
use App\Models\Client;
use Throwable;

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
     *
     * @throws Throwable
     */
    public function authenticate(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate required fields.
        if ($email === '' || $password === '') {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'error' => 'Email et mot de passe obligatoires.',
            ]);

            return;
        }

        // Try backoffice USER first.
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (
            $user
            && (int)$user['is_active'] === 1
            && password_verify($password, $user['password'])
        ) {
            session_regenerate_id(true);

            unset($_SESSION['client']);

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

            return;
        }

        // Try storefront CLIENT.
        $clientModel = new Client();
        $client = $clientModel->findByEmail($email);

        if (
            $client
            && (int)$client['is_active'] === 1
            && !empty($client['password'])
            && password_verify($password, $client['password'])
        ) {
            session_regenerate_id(true);

            unset($_SESSION['user']);

            $_SESSION['client'] = [
                'id' => $client['id'],
                'name' => $client['name'],
                'email' => $client['email'],
            ];

            echo json_encode([
                'success' => true,
                'redirect' => '/public/index.php?route=/account',
            ]);

            return;
        }

        // Authentication failed.
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'error' => 'Email ou mot de passe incorrect.',
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