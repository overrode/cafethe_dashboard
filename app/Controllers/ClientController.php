<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;

class ClientController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        $clientModel = new Client();
        $clients = $clientModel->all();

        $this->view('clients/index', [
            'clients' => $clients,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $this->view('clients/create');
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

        $this->view('clients/edit', [
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
}