<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Client;
use Throwable;
use App\Core\Logger;

/**
 * Class DashboardClientController
 * @package App\Controllers
 */
class DashboardClientController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        $clientModel = new Client();

        $this->view('backend/clients/index', [
            'clients' => $clientModel->all(),
        ]);
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function storeJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!Auth::id()) {
            $this->jsonError(
                'Utilisateur non authentifié.',
                401
            );

            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $favorites = trim($_POST['favorites'] ?? '');
        $abandonedCart = trim($_POST['abandoned_cart'] ?? '');

        // Build the address structure.
        $address = [
            'address' => trim($_POST['address'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
        ];

        // Validate required data.
        if ($name === '') {
            $this->jsonError(
                'Le nom du client est obligatoire.',
                422
            );

            return;
        }

        if (
            $email !== ''
            && !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $this->jsonError(
                'Adresse e-mail invalide.',
                422
            );

            return;
        }

        $clientModel = new Client();

        // Prevent duplicate email.
        if (
            $email !== ''
            && $clientModel->findByEmail($email)
        ) {
            $this->jsonError(
                'Un client avec cette adresse e-mail existe déjà.',
                409
            );

            return;
        }

        try {
            // Create the client.
            $clientId = $clientModel->create([
                'name' => $name,
                'email' => $email ?: null,
                'phone' => $phone ?: null,
                'address' => $address,
                'favorites' => $favorites ?: null,
                'abandoned_cart' => $abandonedCart ?: null,
            ]);

            echo json_encode(
                [
                    'success' => true,
                    'client' => [
                        'id' => $clientId,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'favorites' => $favorites,
                        'abandoned_cart' => $abandonedCart,
                    ],
                ],
                JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $exception) {
            // Log the real server error.
            Logger::exception(
                $exception,
                [
                    'controller' => self::class,
                    'action' => __FUNCTION__,
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

    /**
     * @return void
     * @throws Throwable
     */
    public function updateJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!Auth::id()) {
            $this->jsonError(
                'Utilisateur non authentifié.',
                401
            );

            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $favorites = trim($_POST['favorites'] ?? '');
        $abandonedCart = trim($_POST['abandoned_cart'] ?? '');

        // Build the address structure.
        $address = [
            'address' => trim($_POST['address'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
        ];

        // Validate required data.
        if ($id <= 0 || $name === '') {
            $this->jsonError(
                'Données client invalides.',
                422
            );

            return;
        }

        if (
            $email !== ''
            && !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $this->jsonError(
                'Adresse e-mail invalide.',
                422
            );

            return;
        }

        $clientModel = new Client();
        $client = $clientModel->find($id);

        if (!$client) {
            $this->jsonError(
                'Client introuvable.',
                404
            );

            return;
        }

        // Allow the client's own email.
        if ($email !== '') {
            $existingClient = $clientModel->findByEmail($email);

            if (
                $existingClient
                && (int) $existingClient['id'] !== $id
            ) {
                $this->jsonError(
                    'Un client avec cette adresse e-mail existe déjà.',
                    409
                );

                return;
            }
        }

        try {
            // Update the client.
            $clientModel->update($id, [
                'name' => $name,
                'email' => $email ?: null,
                'phone' => $phone ?: null,
                'address' => $address,
                'favorites' => $favorites ?: null,
                'abandoned_cart' => $abandonedCart ?: null,
            ]);

            echo json_encode(
                [
                    'success' => true,
                    'client' => [
                        'id' => $id,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'favorites' => $favorites,
                        'abandoned_cart' => $abandonedCart,
                    ],
                ],
                JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $exception) {
            // Log the real server error.
            Logger::exception(
                $exception,
                [
                    'controller' => self::class,
                    'action' => __FUNCTION__,
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

    /**
     * @param string $message
     * @param int $status
     * @return void
     */
    private function jsonError(
        string $message,
        int $status
    ): void {
        http_response_code($status);

        echo json_encode(
            [
            'success' => false,
            'error' => $message,
            ],
        JSON_UNESCAPED_UNICODE
        );
    }
}