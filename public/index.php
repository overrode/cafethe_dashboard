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
require_once __DIR__ . '/../app/Controllers/PageClientAuthController.php';
require_once __DIR__ . '/../app/Controllers/PageClientController.php';

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
use App\Controllers\PageClientAuthController;
use App\Controllers\PageClientController;

$router = new Router();

/*
 * FRONTEND
 */

// Public pages.
$router->get('/', [PageHomeController::class, 'index']);
$router->get('/about', [PageAboutController::class, 'index']);
$router->get('/contact', [PageContactController::class, 'index']);
$router->post('/contact/send', [PageContactController::class, 'sendEmail']);

// Storefront products.
$router->get('/products', [PageProductsController::class, 'index']);
$router->get('/product', [PageProductsController::class, 'productPage']);

// Cart and checkout.
$router->get('/cart', [PageCartController::class, 'index']);
$router->post('/checkout', [PageCheckoutController::class, 'index']);
$router->post('/checkout/confirm', [PageCheckoutController::class, 'confirm']);
$router->get('/checkout/success', [PageCheckoutController::class, 'success']);

// Client dashboard.
$router->get('/account', [PageClientController::class, 'index']);
$router->get('/account/orders', [PageClientController::class, 'showOrders']);
$router->get('/account/order', [PageClientController::class, 'showOrder']);
$router->post('/account/deactivate', [PageClientController::class, 'deactivateAccount']);

/*
 * AUTHENTICATION
 */

// Login to back-office modal flow.
$router->get('/login', [DashboardAuthController::class, 'login']);
$router->post('/login', [DashboardAuthController::class, 'authenticate']);
$router->get('/logout', [DashboardAuthController::class, 'logout']);
$router->post('/account/check-email', [PageClientAuthController::class, 'checkEmail']);

// Storefront client account.
$router->get('/account/logout', [PageClientAuthController::class, 'logout']);
$router->get('/account/profile', [PageClientController::class, 'profile']);
$router->post('/account/profile/update', [PageClientController::class, 'updateProfile']);
$router->get('/account/security', [PageClientController::class, 'security']);
$router->post('/account/security/password', [PageClientController::class, 'updatePassword']);


/*
 * DASHBOARD HOME
 */
$router->get('/dashboard', [DashboardHomeController::class, 'index']);


/*
 * DASHBOARD PRODUCTS
 */
$router->get('/dashboard/products', [DashboardProductController::class, 'index']);
$router->post('/dashboard/products/store-json', [DashboardProductController::class, 'storeJson']);
$router->post('/dashboard/products/update-json', [DashboardProductController::class, 'updateJson']);
$router->post('/dashboard/products/set-active-json', [DashboardProductController::class, 'setActiveJson']);


/*
 * DASHBOARD CLIENTS
 */
$router->get('/dashboard/clients', [DashboardClientController::class, 'index']);
$router->post('/dashboard/clients/store-json', [DashboardClientController::class, 'storeJson']);
$router->post('/dashboard/clients/update-json', [DashboardClientController::class, 'updateJson']);


/*
 * DASHBOARD SALES
 */
$router->get('/dashboard/sales', [DashboardSaleController::class, 'index']);
$router->get('/dashboard/sales/create', [DashboardSaleController::class, 'create']);
$router->post('/dashboard/sales/store', [DashboardSaleController::class, 'store']);
$router->post('/dashboard/sales/set-paid', [DashboardSaleController::class, 'setPaid']);
$router->post('/dashboard/sales/set-delivery-status', [DashboardSaleController::class, 'setDeliveryStatus']);
$router->post('/dashboard/sales/set-completed', [DashboardSaleController::class, 'setCompleted']);
$router->post('/dashboard/sales/set-cancelled', [DashboardSaleController::class, 'setCancelled']);
$router->get('/dashboard/sales/sale', [DashboardSaleController::class, 'sale']);

/*
 * DASHBOARD USERS
 */
$router->get('/dashboard/users', [DashboardUserController::class, 'index']);
$router->post('/dashboard/users/store-json', [DashboardUserController::class, 'storeJson']);
$router->post('/dashboard/users/update-json', [DashboardUserController::class, 'updateJson']);
$router->post('/dashboard/users/deactivate-json', [DashboardUserController::class, 'deactivateJson']);

$currentRoute = $_GET['route'] ?? '/';

// Protect every backoffice route.
if (
    str_starts_with($currentRoute, '/dashboard')
    && !isset($_SESSION['user'])
) {
    header('Location: /public/index.php?route=/login');
    exit;
}

// Public client-account routes.
$publicAccountRoutes = [
    '/account/logout',
    '/account/check-email',
];

// Protect client account pages.
if (
    str_starts_with($currentRoute, '/account')
    && !in_array($currentRoute, $publicAccountRoutes, true)
    && !isset($_SESSION['client'])
) {
    header('Location: /public/index.php?route=/');
    exit;
}

$router->dispatch();