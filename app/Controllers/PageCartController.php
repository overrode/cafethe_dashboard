<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Class PageCartController
 */
class PageCartController extends Controller
{

    /**
     * Display the cart page.
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('frontend/cart/index');
    }
}