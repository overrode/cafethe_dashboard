<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use JetBrains\PhpStorm\NoReturn;

/**
 * Handles product-related operations such as displaying products,
 * rendering creation forms, and storing new product data.
 */
class ProductController extends Controller
{
    /**
     * Handles the display of the products index page.
     *
     * @return void
     */
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->all();

        $this->view('products/index', [
            'products' => $products,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $productModel = new Product();
        $categories = $productModel->getCategories();

        $this->view('products/create', [
            'categories' => $categories,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function store(): void
    {
        $productModel = new Product();



        $productModel->create([
            'category_id' => $_POST['category_id'],
            'sku' => trim($_POST['sku']),
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'sale_type' => $_POST['sale_type'],
            'price' => $_POST['price'],
            'vat_rate' => $_POST['vat_rate'],
            'stock' => $_POST['stock'],
            'image' => trim($_POST['image'] ?? ''),
            'origin' => trim($_POST['origin'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        header('Location: /public/index.php?route=/products');
        exit;
    }
}
