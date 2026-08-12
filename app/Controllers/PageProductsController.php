<?php

namespace App\Controllers;

use PDO;
use App\Models\Product;
use App\Core\Controller;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class PageProductsController
 * @package App\Controllers
 */
class PageProductsController extends Controller
{
    /**
     * Display the products page.
     *
     * @return void
     */
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->getActiveProducts();
        $categories = $productModel->getCategoriesFromProducts($products);

        $this->view('frontend/products/index', [
            'title' => 'Nos produits - CafThé',
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Display the product page for a specific product.
     *
     * @return void
     */
    #[NoReturn]
    public function productPage(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        $productModel = new Product();
        $product = $productModel->getActiveProductById($id);

        if (!$product) {
            header('Location: /public/index.php?route=/products');
            exit;
        }

        $this->view('frontend/products/product', [
            'title' => $product['name'] . ' - CafThé',
            'product' => $product,
        ]);
    }
}