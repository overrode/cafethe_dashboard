<?php

declare(strict_types=1);

session_start();
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../config/defines.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Controllers/DashboardProductController.php';
require_once __DIR__ . '/../app/Models/Client.php';
require_once __DIR__ . '/../app/Controllers/DashboardClientController.php';
require_once __DIR__ . '/../app/Models/Sale.php';
require_once __DIR__ . '/../app/Controllers/DashboardSaleController.php';
require_once __DIR__ . '/../app/Models/Dashboard.php';
require_once __DIR__ . '/../app/Controllers/DashboardHomeController.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/DashboardAuthController.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Controllers/DashboardUserController.php';
require_once __DIR__ . '/../app/Controllers/PageHomeController.php';
require_once __DIR__ . '/../app/Controllers/PageAboutController.php';
require_once __DIR__ . '/../app/Controllers/PageContactController.php';
require_once __DIR__ . '/../app/Controllers/PageProductsController.php';
require_once __DIR__ . '/../app/Controllers/PageCartController.php';
require_once __DIR__ . '/../app/Controllers/PageCheckoutController.php';

use App\Core\Router;
use App\Controllers\DashboardProductController;
use App\Controllers\DashboardClientController;
use App\Controllers\DashboardSaleController;
use App\Controllers\DashboardHomeController;
use App\Controllers\DashboardAuthController;
use App\Controllers\DashboardUserController;
use App\Controllers\PageHomeController;
use App\Controllers\PageAboutController;
use App\Controllers\PageContactController;
use App\Controllers\PageProductsController;
use App\Controllers\PageCartController;
use App\Controllers\PageCheckoutController;

$router = new Router();
// Products management
$router->get('/', [DashboardProductController::class, 'index']);
$router->get('/products', [DashboardProductController::class, 'index']);
$router->get('/products/create', [DashboardProductController::class, 'create']);
$router->post('/products/store', [DashboardProductController::class, 'store']);
$router->get('/products/edit', [DashboardProductController::class, 'edit']);
$router->post('/products/update', [DashboardProductController::class, 'update']);
$router->get('/products/deactivate', [DashboardProductController::class, 'deactivate']);

// Clients management
$router->get('/clients', [DashboardClientController::class, 'index']);
$router->get('/clients/create', [DashboardClientController::class, 'create']);
$router->post('/clients/store', [DashboardClientController::class, 'store']);
$router->post('/clients/store-from-sale', [DashboardClientController::class, 'storeFromSale']);
$router->get('/clients/edit', [DashboardClientController::class, 'edit']);
$router->post('/clients/update', [DashboardClientController::class, 'update']);
$router->post('/clients/store-json', [DashboardClientController::class, 'storeJson']);

// Sales management
$router->get('/sales', [DashboardSaleController::class, 'index']);
$router->get('/sales/create', [DashboardSaleController::class, 'create']);
$router->post('/sales/store', [DashboardSaleController::class, 'store']);

// Dashboard
$router->get('/', [DashboardHomeController::class, 'index']);
$router->get('/dashboard', [DashboardHomeController::class, 'index']);

// Authentification
$router->get('/login', [DashboardAuthController::class, 'login']);
$router->get('/logout', [DashboardAuthController::class, 'logout']);
$router->post('/login', [DashboardAuthController::class, 'authenticate']);

//User management
$router->get('/users', [DashboardUserController::class, 'index']);
$router->get('/users/create', [DashboardUserController::class, 'create']);
$router->post('/users/store', [DashboardUserController::class, 'store']);
$router->get('/users/edit', [DashboardUserController::class, 'edit']);
$router->post('/users/update', [DashboardUserController::class, 'update']);
$router->get('/users/deactivate', [DashboardUserController::class, 'deactivate']);

//Pages
$router->get('/', [PageHomeController::class, 'index']);
$router->get('/about', [PageAboutController::class, 'index']);
$router->get('/contact', [PageContactController::class, 'index']);
$router->post('/contact/send', [PageContactController::class, 'sendEmail']);
$router->get('/products', [PageProductsController::class, 'index']);
$router->get('/product', [PageProductsController::class, 'productPage']);

//Cart and Checkout
$router->get('/cart', [PageCartController::class, 'index']);
$router->post('/checkout', [PageCheckoutController::class, 'index']);
$router->post('/checkout/confirm', [PageCheckoutController::class, 'confirm']);
$router->get('/checkout/success', [PageCheckoutController::class, 'success']);

// Sales
$router->post('/sales/set-paid', [DashboardSaleController::class, 'setPaid']);
$router->post('/sales/set-delivery-status', [DashboardSaleController::class, 'setDeliveryStatus']);
$router->post('/sales/set-completed', [DashboardSaleController::class, 'setCompleted']);
$router->post('/sales/set-cancelled', [DashboardSaleController::class, 'setCancelled']);



$publicRoutes = [
    '/login',
    '/logout',
    '/',
    '/about',
    '/contact',
    '/products',
    '/product',
    '/blog',
    '/contact',
    '/contact/send',
    '/cart',
    '/checkout',
    '/checkout/confirm',
    '/checkout/success',
];

$currentRoute = $_GET['route'] ?? '/';

if (!in_array($currentRoute, $publicRoutes, true) && !isset($_SESSION['user'])) {
    header('Location: /public/index.php?route=/login');
    exit;
}

$router->dispatch();
