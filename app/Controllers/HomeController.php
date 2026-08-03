<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

/**
 * HomeController class
 *
 * This controller handles the home page of the application.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $bestSellers = $productModel->getBestSellers();

        $this->view('frontend/home/index', [
            'title' => 'CafThé',
            'bestSellers' => $bestSellers
        ]);
    }
}
