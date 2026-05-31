<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * The Sale class provides methods to manage and interact with sales data,
 * including retrieving all sales and creating new sales with corresponding details.
 */
class Sale
{
    private PDO $db;

    /**
     * Initializes the Sale model by establishing a database connection.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT sales.*, clients.name AS client_name, users.name AS user_name
             FROM sales
             LEFT JOIN clients ON sales.client_id = clients.id
             INNER JOIN users ON sales.user_id = users.id
             ORDER BY sales.sale_date DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        /** If the sale is saved but the stock update fails,
         * the database would be wrong. With a transaction,
         * MySQL cancels everything if one step fails.
         */
        $this->db->beginTransaction();

        try {
            $productStmt = $this->db->prepare(
                'SELECT * FROM products WHERE id = :id AND is_active = 1 LIMIT 1'
            );

            $productStmt->execute([
                'id' => $data['product_id'],
            ]);

            $product = $productStmt->fetch();

            if (!$product) {
                throw new \Exception('Produit introuvable');
            }

            $quantity = (float) $data['quantity'];

            if ($quantity <= 0) {
                throw new \Exception('Quantité invalide');
            }

            if ((float) $product['stock'] < $quantity) {
                throw new \Exception('Stock insuffisant');
            }

            $unitPrice = (float) $product['price'];
            $vatRate = (float) $product['vat_rate'];

            $totalHt = $unitPrice * $quantity;
            $totalVat = $totalHt * ($vatRate / 100);
            $totalTtc = $totalHt + $totalVat;

            $saleStmt = $this->db->prepare(
                'INSERT INTO sales
                (user_id, client_id, status, payment_method, delivery_method, total_ht, total_vat, total_ttc)
                VALUES
                (:user_id, :client_id, :status, :payment_method, :delivery_method, :total_ht, :total_vat, :total_ttc)'
            );

            $saleStmt->execute([
                'user_id' => $data['user_id'],
                'client_id' => $data['client_id'] ?: null,
                'status' => 'completed',
                'payment_method' => $data['payment_method'],
                'delivery_method' => 'magasin',
                'total_ht' => $totalHt,
                'total_vat' => $totalVat,
                'total_ttc' => $totalTtc,
            ]);

            $saleId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO sale_items
                (sale_id, product_id, quantity, unit_price, vat_rate, total_ht, total_vat, total_ttc)
                VALUES
                (:sale_id, :product_id, :quantity, :unit_price, :vat_rate, :total_ht, :total_vat, :total_ttc)'
            );

            $itemStmt->execute([
                'sale_id' => $saleId,
                'product_id' => $data['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'vat_rate' => $vatRate,
                'total_ht' => $totalHt,
                'total_vat' => $totalVat,
                'total_ttc' => $totalTtc,
            ]);

            $stockStmt = $this->db->prepare(
                'UPDATE products SET stock = stock - :quantity WHERE id = :id'
            );

            $stockStmt->execute([
                'quantity' => $quantity,
                'id' => $data['product_id'],
            ]);

            $this->db->commit();

            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();

            die('Erreur vente : ' . $e->getMessage());
        }
    }
}