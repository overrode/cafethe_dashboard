<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;
use App\Core\Auth;
use Throwable;

/**
 * Controller responsible for handling operations related to clients.
 */
class DashboardClientController extends Controller
{

    /**
     * @return void
     */
    public function index(): void
    {
        $clientModel = new Client();
        $clients = $clientModel->all();

        $this->view('backend/clients/index', [
            'clients' => $clients,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $this->view('backend/clients/create');
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function store(): void
    {
        $clientModel = new Client();

        $clientModel->create([
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'favorites' => trim($_POST['favorites'] ?? ''),
            'abandoned_cart' => trim($_POST['abandoned_cart'] ?? ''),
        ]);

        header('Location: /public/index.php?route=/clients');
        exit;
    }

    /**
     * @return void
     */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        $clientModel = new Client();
        $client = $clientModel->find($id);

        if (!$client) {
            echo 'Client introuvable';
            return;
        }

        $this->view('backend/clients/edit', [
            'client' => $client,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        $clientModel = new Client();

        $clientModel->update($id, [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'favorites' => trim($_POST['favorites'] ?? ''),
            'abandoned_cart' => trim($_POST['abandoned_cart'] ?? ''),
        ]);

        header('Location: /public/index.php?route=/clients');
        exit;
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function storeFromSale(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => 'Le nom du client est obligatoire.',
            ]);

            exit;
        }

        $clientModel = new Client();

        $clientId = $clientModel->create([
            'name' => $name,
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'favorites' => '',
            'abandoned_cart' => '',
        ]);

        echo json_encode([
            'success' => true,
            'client' => [
                'id' => $clientId,
                'name' => $name,
            ],
        ]);

        exit;
    }

    /**
     * Store a new client via JSON request.
     * @return void
     */
    public function storeJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!Auth::id()) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'error' => 'Utilisateur non authentifié.',
            ]);

            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');


        /*
         * Name is the only required field for a dashboard client.
         */
        if ($name === '') {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'error' => 'Le nom du client est obligatoire.',
            ]);

            return;
        }


        /*
         * Email is optional but must be valid when supplied.
         */
        if (
            $email !== ''
            && !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'error' => 'Adresse e-mail invalide.',
            ]);

            return;
        }

        $clientModel = new Client();


        /*
         * Avoid creating another client with the same email.
         */
        if ($email !== '') {
            $existingClient = $clientModel->findByEmail($email);

            if ($existingClient) {
                http_response_code(409);

                echo json_encode([
                    'success' => false,
                    'error' => 'Un client avec cette adresse e-mail existe déjà.',
                ]);

                return;
            }
        }


        try {
            $clientId = $clientModel->create([
                'name' => $name,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
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
                    ],
                ],
                JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $exception) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}