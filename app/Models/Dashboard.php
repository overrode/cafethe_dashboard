<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Class Dashboard
 * Provides methods to generate statistics and retrieve data related to
 * sales, clients, and products for the application's dashboard.
 */
class Dashboard
{
    private PDO $db;

    /**
     *
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retrieves statistical data including sales count, total revenue, average basket value,
     * number of clients, and count of active products.
     *
     * @return array An associative array containing the following keys:
     *               - sales_count: Total number of sales.
     *               - revenue: Total revenue from sales.
     *               - average_basket: Average value of sales baskets.
     *               - clients_count: Total number of clients.
     *               - active_products_count: Count of active products in the inventory.
     */
    public function getStats(): array
    {
        $salesCount = $this->db->query('SELECT COUNT(*) FROM sales')->fetchColumn();
        $revenue = $this->db->query('SELECT COALESCE(SUM(total_ttc), 0) FROM sales')->fetchColumn();
        $averageBasket = $this->db->query('SELECT COALESCE(AVG(total_ttc), 0) FROM sales')->fetchColumn();
        $clientsCount = $this->db->query('SELECT COUNT(*) FROM clients')->fetchColumn();
        $activeProductsCount = $this->db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn();

        return [
            'sales_count' => $salesCount,
            'revenue' => $revenue,
            'average_basket' => $averageBasket,
            'clients_count' => $clientsCount,
            'active_products_count' => $activeProductsCount,
        ];
    }

    /**
     * Retrieves a list of products with stock levels equal to or below a specified threshold.
     *
     * @param int $threshold The stock level threshold used to filter products. Default is 5.
     * @return array An array of products with their associated category names that meet the stock criteria.
     */
    public function getLowStockProducts(int $threshold = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT products.*, categories.name AS category_name
             FROM products
             INNER JOIN categories ON products.category_id = categories.id
             WHERE products.stock <= :threshold
             AND products.is_active = 1
             ORDER BY products.stock ASC'
        );

        $stmt->execute([
            'threshold' => $threshold,
        ]);

        return $stmt->fetchAll();
    }
}