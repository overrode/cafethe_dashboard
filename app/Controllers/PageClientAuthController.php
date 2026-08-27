<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use JetBrains\PhpStorm\NoReturn;
use App\Models\Client;
use Throwable;

/**
 * Class PageClientAuthController
 */
class PageClientAuthController extends Controller
{

    /**
     * @return void
     */
    #[NoReturn]
    public function logout(): void
    {
        unset($_SESSION['client']);

        header('Location: /public/index.php?route=/');
        exit;
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function checkEmail(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $email = trim($_POST['email'] ?? '');

        if (
            $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            echo json_encode([
                'exists' => false,
            ]);

            return;
        }

        $clientModel = new Client();

        echo json_encode([
            'exists' => (bool) $clientModel->findByEmail($email),
        ]);
        }
}