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
     * Retrieves all sales records from the database, including associated client and user names.
     *
     * @return array An array of sales records with client and user information.
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
     * Creates a new sale record along with its associated items and updates product stock.
     *
     * @param array $data The sale data including user_id, client_id, product_id, quantity, etc.
     * @return bool True if the sale was created successfully, false otherwise.
     */
    public function create(array $data): bool
    {
        /** If the sale is saved but the stock update fails,
         * the database would be wrong. With a transaction,
         * MySQL cancels everything if one step fails.
         */
        $this->db->beginTransaction();

        try {

            $items = $data['items'] ?? [];

            if (empty($items)) {
                throw new \Exception('Aucun produit dans la vente');
            }

            $totalHt = 0.0;
            $totalVat = 0.0;
            $totalTtc = 0.0;

            $saleItems = [];

            $productModel = new Product();

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (float) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new \Exception('Produit ou quantité invalide');
                }

                $product = $productModel->getActiveProductById($productId);

                if (!$product) {
                    throw new \Exception('Produit introuvable');
                }

                if ((float) $product['stock'] < $quantity) {
                    throw new \Exception(
                        'Stock insuffisant pour le produit : ' . $product['name']
                    );
                }

                if (
                    !isset($product['price'], $product['vat_rate']) ||
                    !is_numeric($product['price']) ||
                    !is_numeric($product['vat_rate'])
                ) {
                    throw new \Exception(
                        'Prix ou TVA invalide pour le produit : ' . $product['name']
                    );
                }

                $unitPrice = (float) $product['price'];
                $vatRate = (float) $product['vat_rate'];

                if ($unitPrice < 0 || $vatRate < 0 || $vatRate > 100) {
                    throw new \Exception(
                        'Prix ou TVA invalide pour le produit : ' . $product['name']
                    );
                }

                $itemTotalHt = $unitPrice * $quantity;
                $itemTotalVat = $itemTotalHt * ($vatRate / 100);
                $itemTotalTtc = $itemTotalHt + $itemTotalVat;

                $totalHt += $itemTotalHt;
                $totalVat += $itemTotalVat;
                $totalTtc += $itemTotalTtc;

                $saleItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'vat_rate' => $vatRate,
                    'total_ht' => $itemTotalHt,
                    'total_vat' => $itemTotalVat,
                    'total_ttc' => $itemTotalTtc,
                ];
            }

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
            $stockStmt = $this->db->prepare(
                'UPDATE products
                 SET stock = stock - :quantity
                 WHERE id = :id
                 AND stock >= :required_quantity'
            );

            foreach ($saleItems as $item) {
                $itemStmt->execute([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $item['vat_rate'],
                    'total_ht' => $item['total_ht'],
                    'total_vat' => $item['total_vat'],
                    'total_ttc' => $item['total_ttc'],
                ]);

                $stockStmt->execute([
                    'quantity' => $item['quantity'],
                    'required_quantity' => $item['quantity'],
                    'id' => $item['product_id'],
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new \Exception('Stock insuffisant lors de la mise à jour');
                }
            }

            $this->db->commit();

            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();

            die('Erreur vente : ' . $e->getMessage());
        }
    }
}