<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;

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
}