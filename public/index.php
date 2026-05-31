<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';
require_once __DIR__ . '/../app/Models/Client.php';
require_once __DIR__ . '/../app/Controllers/ClientController.php';
require_once __DIR__ . '/../app/Models/Sale.php';
require_once __DIR__ . '/../app/Controllers/SaleController.php';

use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\ClientController;
use App\Controllers\SaleController;

$router = new Router();

$router->get('/', [ProductController::class, 'index']);
// Products management
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/store', [ProductController::class, 'store']);
$router->get('/products/edit', [ProductController::class, 'edit']);
$router->post('/products/update', [ProductController::class, 'update']);
$router->get('/products/deactivate', [ProductController::class, 'deactivate']);

// Clients management
$router->get('/clients', [ClientController::class, 'index']);
$router->get('/clients/create', [ClientController::class, 'create']);
$router->post('/clients/store', [ClientController::class, 'store']);
$router->get('/clients/edit', [ClientController::class, 'edit']);
$router->post('/clients/update', [ClientController::class, 'update']);

// Sales management
$router->get('/sales', [SaleController::class, 'index']);
$router->get('/sales/create', [SaleController::class, 'create']);
$router->post('/sales/store', [SaleController::class, 'store']);

$router->dispatch();
