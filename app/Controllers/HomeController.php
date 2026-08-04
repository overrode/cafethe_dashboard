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
        $popularProducts = $productModel->getPopularProducts();
        $popularCategories = $productModel->getCategoriesFromProducts($popularProducts);

        $this->view('frontend/home/index', [
            'title' => 'CafThé',
            'bestSellers' => $popularProducts,
            'popularCategories' => $popularCategories,
        ]);
    }
}
