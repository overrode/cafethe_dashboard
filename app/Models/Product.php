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

    /**
     * @return array
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
}
