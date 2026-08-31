<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;
use Throwable;
use App\Core\Logger;

/**
 * User Controller Class
 */
class DashboardUserController extends Controller
{
    /**
     * @var array<string>
     */
    private const ROLES = [
        'admin',
        'vendeur',
    ];

    /**
     * Require administrator access.
     *
     * @return void
     */
    private function requireAdmin(): void
    {
        if (Auth::isAdmin()) {
            return;
        }

        Logger::warning(
            'Accès administrateur refusé.',
            [
                'controller' => self::class,
                'user_id' => Auth::id(),
                'route' => $_GET['route'] ?? null,
            ]
        );

        http_response_code(403);

        require ROOT_PATH . '/app/Views/errors/403.php';

        exit;
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $userModel = new User();
        $users = $userModel->all();

        $this->view('backend/users/index', [
            'users' => $users,
        ]);
    }

    /**
     * @return void
     */
    public function storeJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Only admins can manage dashboard users.
        if (!Auth::isAdmin()) {
            $this->jsonError('Accès refusé.', 403);
            return;
        }

        // Read and normalize submitted values.
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $isActive = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

        // Required fields for a new user.
        if (
            $name === ''
            || $email === ''
            || $password === ''
        ) {
            $this->jsonError(
                'Nom, email et mot de passe obligatoires.',
                422
            );

            return;
        }

        // Validate email format.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Adresse e-mail invalide.', 422);
            return;
        }

        // Allow only known dashboard roles.
        if (!in_array($role, self::ROLES, true)) {
            $this->jsonError('Rôle invalide.', 422);
            return;
        }

        $userModel = new User();

        // Prevent duplicate email accounts.
        if ($userModel->findByEmail($email)) {
            $this->jsonError(
                'Un utilisateur avec cette adresse e-mail existe déjà.',
                409
            );

            return;
        }

        try {
            // Create the user in the database.
            $userId = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'is_active' => $isActive,
            ]);

            // Return the created user to React.
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'is_active' => $isActive,
                ],
            ]);

        } catch (Throwable $exception) {
            $this->jsonException(
                $exception,
                __FUNCTION__
            );
        }
    }

    /**
     * @return void
     */
    public function updateJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!Auth::isAdmin()) {
            $this->jsonError('Accès refusé.', 403);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $isActive = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

        if (
            $id <= 0
            || $name === ''
            || $email === ''
        ) {
            $this->jsonError(
                'Données utilisateur invalides.',
                422
            );

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Adresse e-mail invalide.', 422);
            return;
        }

        if (!in_array($role, self::ROLES, true)) {
            $this->jsonError('Rôle invalide.', 422);
            return;
        }

        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            $this->jsonError('Utilisateur introuvable.', 404);
            return;
        }

        if (
            $id === Auth::id()
            && (
                !$isActive
                || $role !== $user['role']
            )
        ) {
            $this->jsonError(
                'Vous ne pouvez pas désactiver ou modifier votre propre rôle.',
                422
            );

            return;
        }

        $existingUser = $userModel->findByEmail($email);

        if (
            $existingUser
            && (int)$existingUser['id'] !== $id
        ) {
            $this->jsonError(
                'Un utilisateur avec cette adresse e-mail existe déjà.',
                409
            );

            return;
        }

        try {
            $userModel->update($id, [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'is_active' => $isActive,
            ]);

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'is_active' => $isActive,
                ],
            ]);

        } catch (Throwable $exception) {
            $this->jsonException(
                $exception,
                __FUNCTION__
            );
        }
    }

    /**
     * @return void
     */
    public function deactivateJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!Auth::isAdmin()) {
            $this->jsonError('Accès refusé.', 403);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->jsonError('Utilisateur invalide.', 422);
            return;
        }

        if ($id === Auth::id()) {
            $this->jsonError(
                'Vous ne pouvez pas vous désactiver.',
                422
            );

            return;
        }

        $userModel = new User();

        if (!$userModel->find($id)) {
            $this->jsonError('Utilisateur introuvable.', 404);
            return;
        }

        try {
            $userModel->deactivate($id);

            echo json_encode([
                'success' => true,
                'id' => $id,
            ]);

        } catch (Throwable $exception) {
            $this->jsonException(
                $exception,
                __FUNCTION__
            );
        }
    }

    /**
     * @param string $message
     * @param int $status
     * @return void
     */
    private function jsonError(
        string $message,
        int    $status
    ): void
    {
        http_response_code($status);

        echo json_encode(
            [
            'success' => false,
            'error' => $message,
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Handle unexpected JSON errors.
     *
     * @param Throwable $exception
     * @param string $action
     * @return void
     */
    private function jsonException(Throwable $exception, string $action): void
    {
        Logger::exception(
            $exception,
            [
                'controller' => self::class,
                'action' => $action,
                'user_id' => Auth::id(),
            ]
        );

        $this->jsonError(
            IS_DEVELOPMENT
                ? $exception->getMessage()
                : 'Une erreur est survenue.',
            500
        );
    }
}