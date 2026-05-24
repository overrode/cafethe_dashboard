<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Represents a product entity and provides methods
 * to interact with the database for product-related operations.
 */
class Product
{
    /**
     * @var PDO
     */
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retrieves all products from the database along with their associated category names.
     *
     * @return array An array of products, including category names, sorted in descending order by product ID.
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT products.*, categories.name AS category_name
             FROM products
             INNER JOIN categories ON products.category_id = categories.id
             ORDER BY products.id DESC'
        );

        return $stmt->fetchAll();
    }
}
