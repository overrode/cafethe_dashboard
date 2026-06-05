<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

/**
 * User Controller Class
 */
class UserController extends Controller
{
    /**
     * @return void
     */
    private function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo 'Accès refusé';
            exit;
        }
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $userModel = new User();
        $users = $userModel->all();

        $this->view('users/index', [
            'users' => $users,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $this->requireAdmin();

        $this->view('users/create');
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function store(): void
    {
        $this->requireAdmin();

        $userModel = new User();

        $userModel->create([
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        header('Location: /public/index.php?route=/users');
        exit;
    }

    /**
     * @return void
     */
    public function edit(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            echo 'Utilisateur introuvable';
            return;
        }

        $this->view('users/edit', [
            'user' => $user,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function update(): void
    {
        $this->requireAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $userModel = new User();

        $userModel->update($id, [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        header('Location: /public/index.php?route=/users');
        exit;
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function deactivate(): void
    {
        $this->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0 && $id !== Auth::id()) {
            $userModel = new User();
            $userModel->deactivate($id);
        } else {
            http_response_code(400);
            echo 'Utilisateur ne peut pas être désactivé';
            exit;
        }

        header('Location: /public/index.php?route=/users');
        exit;
    }
}