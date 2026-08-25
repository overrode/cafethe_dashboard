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
    /**
     * @var PDO Database connection instance used for executing queries.
     */
    private PDO $db;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
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

    /**
     * Main dashboard KPIs.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stmt = $this->db->query(
            "
        SELECT
            (
                SELECT COUNT(*)
                FROM sales
                WHERE payment_status = 'paid'
            ) AS sales_count,

            (
                SELECT COALESCE(SUM(total_ttc), 0)
                FROM sales
                WHERE payment_status = 'paid'
            ) AS revenue,

            (
                SELECT COALESCE(AVG(total_ttc), 0)
                FROM sales
                WHERE payment_status = 'paid'
            ) AS average_basket,

            (
                SELECT COUNT(*)
                FROM clients
            ) AS clients_count,

            (
                SELECT COUNT(*)
                FROM products
                WHERE is_active = 1
            ) AS active_products_count,

            (
                SELECT COUNT(*)
                FROM sales
                WHERE payment_status = 'pending'
                AND status != 'cancelled'
            ) AS pending_payments_count,

            (
                SELECT COUNT(*)
                FROM sales
                WHERE status = 'preparing'
            ) AS preparing_sales_count
        "
        );

        return $stmt->fetch();
    }

    /**
     * Latest orders for the dashboard overview.
     *
     * @param int $limit
     * @return array
     */
    public function getRecentSales(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $stmt = $this->db->query(
            "
            SELECT
                s.id,
                s.sale_date,
                s.total_ttc,
                s.status,
                s.payment_status,
                s.source,
                c.name AS client_name
            FROM sales s
            LEFT JOIN clients c ON c.id = s.client_id
            ORDER BY s.sale_date DESC
            LIMIT $limit
            "
        );

        return $stmt->fetchAll();
    }

    /**
     * Clients with the highest paid revenue.
     *
     * @param int $limit
     * @return array
     */
    public function getTopClients(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $stmt = $this->db->query(
            "
            SELECT
                c.id,
                c.name,
                COUNT(s.id) AS sales_count,
                SUM(s.total_ttc) AS revenue
            FROM sales s
            INNER JOIN clients c ON c.id = s.client_id
            WHERE s.payment_status = 'paid'
            GROUP BY c.id, c.name
            ORDER BY revenue DESC
            LIMIT $limit
            "
        );

        return $stmt->fetchAll();
    }

    /**
     * Products with the highest quantity sold.
     *
     * @param int $limit
     * @return array
     */
    public function getTopProducts(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $stmt = $this->db->query(
            "
            SELECT
                p.id,
                p.name,
                p.sku,
                SUM(si.quantity) AS quantity_sold
            FROM sale_items si
            INNER JOIN sales s ON s.id = si.sale_id
            INNER JOIN products p ON p.id = si.product_id
            WHERE s.payment_status = 'paid'
            GROUP BY p.id, p.name, p.sku
            ORDER BY quantity_sold DESC
            LIMIT $limit
            "
        );

        return $stmt->fetchAll();
    }
}