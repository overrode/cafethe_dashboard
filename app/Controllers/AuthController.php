<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends Controller
{
    /**
     * @return void
     */
    public function login(): void
    {
        $this->view('auth/login');
    }

    /**
     * @return void
     */
    public function authenticate(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Email ou mot de passe incorrect.';

            $this->view('auth/login', [
                'error' => $error,
            ]);

            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        header('Location: /public/index.php?route=/dashboard');
        exit;
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function logout(): void
    {
        session_destroy();

        header('Location: /public/index.php?route=/login');
        exit;
    }
}