<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;
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
     * Return a JSON error response.
     *
     * @param string $message
     * @param int $status
     * @return void
     */
    private function jsonError(string $message, int $status): void
    {
        http_response_code($status);

        echo json_encode([
            'success' => false,
            'error' => $message,
        ]);
    }

}