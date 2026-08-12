<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

/**
 * PageHomeController class
 *
 * This controller handles the home page of the application.
 */
class PageHomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $popularProducts = $productModel->getPopularProducts();
        $popularCategories = $productModel->getCategoriesFromProducts($popularProducts);

        $this->view('frontend/home/index', [
            'title' => 'CafThé',
            'popularProducts' => $popularProducts,
            'popularCategories' => $popularCategories,
        ]);
    }
}
