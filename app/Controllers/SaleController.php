<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use JetBrains\PhpStorm\NoReturn;

/**
 * Controller responsible for handling operations related to sales.
 */
class SaleController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        $saleModel = new Sale();
        $sales = $saleModel->all();

        $this->view('sales/index', [
            'sales' => $sales,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $clientModel = new Client();
        $productModel = new Product();

        $clients = $clientModel->all();
        $products = $productModel->all();

        $this->view('sales/create', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function store(): void
    {
        $saleModel = new Sale();

        $saleModel->create([
            'user_id' => 1,
            'client_id' => $_POST['client_id'] ?? null,
            'product_id' => $_POST['product_id'],
            'quantity' => $_POST['quantity'],
            'payment_method' => $_POST['payment_method'],
        ]);

        header('Location: /public/index.php?route=/sales');
        exit;
    }
}
