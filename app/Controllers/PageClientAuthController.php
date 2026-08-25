<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use JetBrains\PhpStorm\NoReturn;

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
}