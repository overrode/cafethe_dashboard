<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;

/**
 * Class PageClientController
 */
class PageClientController extends Controller
{

    /**
     * Display the client dashboard.
     *
     * @return void
     */
    public function index(): void
    {
        $clientId = (int) $_SESSION['client']['id'];

        // Load the current client.
        $clientModel = new Client();
        $client = $clientModel->find($clientId);

        if (!$client) {
            unset($_SESSION['client']);

            header('Location: /public/index.php?route=/');
            exit;
        }

        // Show the client dashboard.
        $this->view('frontend/account/index', [
            'client' => $client,
        ]);
    }
}