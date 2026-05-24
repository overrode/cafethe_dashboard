<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->all();

        $this->view('products/index', [
            'products' => $products,
        ]);
    }
}
