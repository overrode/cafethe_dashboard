<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dashboard;

/**
 * Handles the display of the dashboard homepage.
 *
 * The `index` method retrieves statistical data and low-stock product information
 * from the dashboard model and renders them to the corresponding view.
 */
class DashboardController extends Controller
{
    /**
     * Renders the dashboard index view with statistical data and low stock product information.
     *
     * @return void
     */
    public function index(): void
    {
        $dashboardModel = new Dashboard();

        $stats = $dashboardModel->getStats();
        $lowStockProducts = $dashboardModel->getLowStockProducts(5);

        $this->view('dashboard/index', [
            'stats' => $stats,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}