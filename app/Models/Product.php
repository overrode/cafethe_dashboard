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

    /**
     * The maximum number of best-selling products to retrieve.
     */
    const BEST_SALE_LIMIT = 6;

    /**
     * Product constructor.
     * Initializes the database connection.
     */
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

    /**
     * Retrieves all categories from the database.
     *
     * @return array An array of categories sorted in ascending order by name.
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name ASC');

        return $stmt->fetchAll();
    }

    /**
     * Inserts a new product into the database with the provided data.
     *
     * @param array $data An associative array containing the product details, including:
     *                    - category_id (int): The category ID of the product.
     *                    - sku (string): The SKU (Stock Keeping Unit) of the product.
     *                    - name (string): The name of the product.
     *                    - description (string): The product description.
     *                    - sale_type (string): The type of sale for the product.
     *                    - price (float): The price of the product.
     *                    - vat_rate (float): The VAT rate applied to the product.
     *                    - stock (int): The stock quantity of the product.
     *                    - image (string|null): The optional image URL or path for the product.
     *                    - origin (string|null): The optional origin of the product.
     *                    - is_active (int|null): The active status of the product (1 or 0).
     *
     * @return bool True on successful insertion, false otherwise.
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products 
            (category_id, sku, name, description, sale_type, price, vat_rate, stock, image, origin, is_active)
            VALUES 
            (:category_id, :sku, :name, :description, :sale_type, :price, :vat_rate, :stock, :image, :origin, :is_active)'
        );

        return $stmt->execute([
            'category_id' => $data['category_id'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'],
            'sale_type' => $data['sale_type'],
            'price' => $data['price'],
            'vat_rate' => $data['vat_rate'],
            'stock' => $data['stock'],
            'image' => $data['image'] ?? null,
            'origin' => $data['origin'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
        ]);
    }

    /**
     * Finds a product by its unique identifier.
     *
     * @param int $id The unique identifier of the product to find.
     * @return array|null An associative array of the product data if found, or null if the product does not exist.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $product = $stmt->fetch();

        return $product ?: null;
    }

    /**
     * Updates a product's details by its unique identifier.
     *
     * @param int $id The unique identifier of the product to update.
     * @param array $data An associative array containing the product's updated details, such as:
     *                    - category_id: int The ID of the product's category.
     *                    - sku: string The stock-keeping unit of the product.
     *                    - name: string The name of the product.
     *                    - description: string The description of the product.
     *                    - sale_type: string The sale type of the product.
     *                    - price: float The price of the product.
     *                    - vat_rate: float The VAT rate applied to the product.
     *                    - stock: int The stock quantity of the product.
     *                    - image: string|null The URL or path to the product's image.
     *                    - origin: string|null The origin of the product.
     *                    - is_active: bool Whether the product is active.
     * @return bool True if the update was successful, or false otherwise.
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 sku = :sku,
                 name = :name,
                 description = :description,
                 sale_type = :sale_type,
                 price = :price,
                 vat_rate = :vat_rate,
                 stock = :stock,
                 image = :image,
                 origin = :origin,
                 is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'],
            'sale_type' => $data['sale_type'],
            'price' => $data['price'],
            'vat_rate' => $data['vat_rate'],
            'stock' => $data['stock'],
            'image' => $data['image'] ?: null,
            'origin' => $data['origin'] ?: null,
            'is_active' => $data['is_active'],
        ]);
    }

    /**
     * Deactivates a product by setting its 'is_active' status to 0.
     *
     * @param int $id The unique identifier of the product to deactivate.
     * @return bool True if the deactivation was successful, or false otherwise.
     */
    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET is_active = 0 WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
        ]);
    }

    /**
     * Retrieves the most popular products based on total sales quantity.
     *
     * @param int $limit The maximum number of popular products to retrieve. Defaults to BEST_SALE_LIMIT.
     * @return array An array of popular products, each including product details, category name, and total sales quantity (TS).
     */
    public function getPopularProducts(int $limit = self::BEST_SALE_LIMIT): array
    {
        $sql = 'SELECT
                    P.*,
                    C.name AS category_name,
                    SUM(SI.quantity) AS TS
                FROM products AS P
                INNER JOIN sale_items AS SI
                    ON SI.product_id = P.id
                INNER JOIN categories AS C
                    ON C.id = P.category_id
                WHERE P.is_active = 1
                GROUP BY
                    P.id,
                    C.name
                ORDER BY TS DESC
                LIMIT :limit';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Extracts unique categories from a list of products.
     *
     * @param array $products An array of products, each containing 'category_id' and 'category_name'.
     * @return array An associative array where keys are category IDs and values are category names.
     */
    public function getCategoriesFromProducts(array $products): array
    {
        $categories = [];
        foreach ($products as $product) {
            $categoryId = (int)$product['category_id'];
            $categories[$categoryId] = $product['category_name'];
        }

        return $categories;
    }

    /**
     * Retrieves all active products from the database along with their associated category names.
     *
     * @return array An array of active products, including category names, sorted in ascending order by product name.
     */
    public function getActiveProducts(): array
    {
        $sql = "
            SELECT
                p.*,
                c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON c.id = p.category_id
            WHERE p.is_active = 1
            ORDER BY p.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retrieves an active product by its unique identifier along with its associated category name.
     *
     * @param int $id The unique identifier of the product to retrieve.
     * @return array|null An associative array of the product data if found and active, or null if the product does not exist or is inactive.
     */
    public function getActiveProductById(int $id): ?array
    {
        $sql = "
            SELECT
                p.*,
                c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON c.id = p.category_id
            WHERE
                p.id = :id
                AND p.is_active = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $product = $stmt->fetch();

        return $product ?: null;
    }

    /**
     * Retrieves active products by their unique identifiers.
     *
     * @param array $ids An array of unique product identifiers to retrieve.
     * @return array An array of active products, each represented as an associative array, or an empty array if no active products are found.
     */
    public function getActiveProductsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(
            ',',
            array_fill(0, count($ids), '?')
        );

        $sql = "
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id IN ($placeholders)
            AND p.is_active = 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }

    /**
     * Retrieves all active products that are available for sale (i.e., have stock greater than zero).
     * @return array
     */
    public function getActiveProductsForSale(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1
             AND p.stock > 0
             ORDER BY p.name'
        );

        return $stmt->fetchAll();
    }
}
