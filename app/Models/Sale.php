<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use Throwable;

/**
 * Class Sale
 */
class Sale
{
    /**
     * @var PDO The database connection.
     */
    private PDO $db;


    /**
     * @var array<string> The possible statuses for a sale.
     */
    private const STATUSES = [
        'pending',
        'preparing',
        'completed',
        'cancelled',
    ];

    /**
     * @var array<string> The possible sources for a sale.
     */
    private const SOURCES = [
        'dashboard',
        'website',
    ];

    /**
     * @var array<string> The possible payment statuses for a sale.
     */
    private const PAYMENT_STATUSES = [
        'pending',
        'paid',
        'failed',
        'refunded',
    ];

    /**
     * @var array<string> The possible payment methods for a sale.
     */
    private const PAYMENT_METHODS = [
        'cb',
        'especes',
        'cheque',
        'virement',
    ];

    /**
     * @var array<string> The possible delivery methods for a sale.
     */
    private const DELIVERY_METHODS = [
        'magasin',
        'livraison',
    ];

    /**
     * @var array<string> The possible delivery statuses for a sale.
     */
    private const DELIVERY_STATUSES = [
        'pending',
        'ready_for_pickup',
        'shipped',
        'delivered',
        'collected',
    ];

    /**
     * @var array<string, array<string>> The possible delivery statuses for each delivery method.
     */
    private const DELIVERY_STATUSES_BY_METHOD = [
        'livraison' => [
            'pending',
            'shipped',
            'delivered',
        ],

        'magasin' => [
            'pending',
            'ready_for_pickup',
            'collected',
        ],
    ];

    /*
     * @var array<string, array<string, string>> The valid delivery status transitions for each delivery method.
     */
    private const DELIVERY_TRANSITIONS_BY_METHOD = [
        'livraison' => [
            'pending' => 'shipped',
            'shipped' => 'delivered',
        ],

        'magasin' => [
            'pending' => 'ready_for_pickup',
            'ready_for_pickup' => 'collected',
        ],
    ];

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
            'SELECT
                sales.*,
                clients.name AS client_name,
                users.name AS user_name
            FROM sales
            LEFT JOIN clients
                ON sales.client_id = clients.id
            LEFT JOIN users
                ON sales.user_id = users.id
            ORDER BY sales.sale_date DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Creates a sale, its sale items and updates stock when payment is already confirmed.
     *
     * @param array $data
     * @return int The ID of the created sale.
     * @throws Throwable
     */
    public function create(array $data): int
    {
        /*
         * Everything is executed inside one transaction.
         *
         * If any part fails, the sale, its items and stock changes
         * are all rolled back together.
         */
        $this->db->beginTransaction();

        try {
            /*
             * Validate that the sale contains products.
             */
            $items = $data['items'] ?? [];

            if (!is_array($items) || empty($items)) {
                throw new Exception('Aucun produit dans la vente');
            }


            /*
             * Validate the general sale information.
             *
             * These values are required explicitly so the Sale model
             * never has to guess if the sale comes from the dashboard
             * or from the website.
             */
            $status = $data['status']
                ?? throw new Exception('Statut de vente manquant');

            $paymentStatus = $data['payment_status']
                ?? throw new Exception('Statut de paiement manquant');

            $paymentMethod = $data['payment_method']
                ?? throw new Exception('Mode de paiement manquant');

            $deliveryMethod = $data['delivery_method']
                ?? throw new Exception('Mode de livraison manquant');

            $source = $data['source']
                ?? throw new Exception('Source de vente manquante');

            $deliveryStatus = $data['delivery_status']
                ?? throw new Exception('Statut de livraison manquant');

            if (
                !in_array(
                    $deliveryStatus,
                    self::DELIVERY_STATUSES_BY_METHOD[$deliveryMethod],
                    true
                )
            ) {
                throw new Exception(
                    'Statut de livraison incompatible avec le mode de livraison'
                );
            }


            /*
             * Verify that all supplied values correspond to values
             * supported by the database.
             */
            if (!in_array($status, self::STATUSES, true)) {
                throw new Exception('Statut de vente invalide');
            }

            if (!in_array($paymentStatus, self::PAYMENT_STATUSES, true)) {
                throw new Exception('Statut de paiement invalide');
            }

            if (!in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
                throw new Exception('Mode de paiement invalide');
            }

            if (!in_array($deliveryMethod, self::DELIVERY_METHODS, true)) {
                throw new Exception('Mode de livraison invalide');
            }

            if (!in_array($source, self::SOURCES, true)) {
                throw new Exception('Source de vente invalide');
            }

            if (!in_array($deliveryStatus, self::DELIVERY_STATUSES, true)) {
                throw new Exception('Statut de livraison invalide');
            }


            /*
             * Normalize the requested products.
             *
             * Duplicate products are merged into a single quantity.
             *
             * Example:
             * Product 4 × 2
             * Product 4 × 3
             *
             * becomes:
             * Product 4 × 5
             */
            $quantitiesByProductId = [];

            foreach ($items as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $quantity = (float)($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new Exception(
                        'Produit ou quantité invalide'
                    );
                }

                if (!isset($quantitiesByProductId[$productId])) {
                    $quantitiesByProductId[$productId] = 0.0;
                }

                $quantitiesByProductId[$productId] += $quantity;
            }


            /*
             * Load every required product using ONE database query.
             *
             * This avoids making one query for every sale item.
             */
            $productModel = new Product();

            $products = $productModel->getActiveProductsByIds(
                array_keys($quantitiesByProductId)
            );


            /*
             * Index products by ID so they can be found immediately
             * during the validation/calculation loop.
             */
            $productsById = [];

            foreach ($products as $product) {
                $productsById[(int)$product['id']] = $product;
            }


            /*
             * Build trusted sale items and totals.
             *
             * Price, VAT and stock always come from MySQL,
             * never from the browser.
             */
            $saleItems = [];

            $totalHt = 0.0;
            $totalVat = 0.0;
            $totalTtc = 0.0;

            foreach ($quantitiesByProductId as $productId => $quantity) {
                $product = $productsById[$productId] ?? null;

                if (!$product) {
                    throw new Exception(
                        'Produit introuvable'
                    );
                }


                /*
                 * Check current stock availability.
                 *
                 * For pending website orders this does NOT reserve the stock.
                 * Stock will be checked again when payment succeeds.
                 */
                if ((float)$product['stock'] < $quantity) {
                    throw new Exception(
                        'Stock insuffisant pour le produit : '
                        . $product['name']
                    );
                }


                /*
                 * Validate price and VAT before doing financial calculations.
                 */
                if (
                    !isset(
                        $product['price'],
                        $product['vat_rate']
                    )
                    || !is_numeric($product['price'])
                    || !is_numeric($product['vat_rate'])
                ) {
                    throw new Exception(
                        'Prix ou TVA invalide pour le produit : '
                        . $product['name']
                    );
                }

                $unitPrice = (float)$product['price'];
                $vatRate = (float)$product['vat_rate'];

                if (
                    $unitPrice < 0
                    || $vatRate < 0
                    || $vatRate > 100
                ) {
                    throw new Exception(
                        'Prix ou TVA invalide pour le produit : '
                        . $product['name']
                    );
                }


                /*
                 * Calculate HT, VAT and TTC for this sale line.
                 */
                $itemTotalHt = $unitPrice * $quantity;
                $itemTotalVat = $itemTotalHt * ($vatRate / 100);
                $itemTotalTtc = $itemTotalHt + $itemTotalVat;


                /*
                 * Add the line totals to the complete sale totals.
                 */
                $totalHt += $itemTotalHt;
                $totalVat += $itemTotalVat;
                $totalTtc += $itemTotalTtc;


                /*
                 * Store the trusted line data that will later
                 * be inserted into sale_items.
                 */
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


            /*
             * A paid sale receives its payment timestamp immediately.
             *
             * Pending/failed payments keep paid_at as NULL.
             */
            $paidAt = $paymentStatus === 'paid'
                ? date('Y-m-d H:i:s')
                : null;


            /*
             * Create the parent sales record.
             *
             * exported_at is intentionally omitted.
             * New sales automatically start with exported_at = NULL.
             */
            $saleStmt = $this->db->prepare(
                'INSERT INTO sales
                (
                    user_id,
                    client_id,
                    status,
                    payment_status,
                    paid_at,
                    payment_method,
                    delivery_method,
                    delivery_status,
                    source,
                    total_ht,
                    total_vat,
                    total_ttc
                )
                VALUES
                (
                    :user_id,
                    :client_id,
                    :status,
                    :payment_status,
                    :paid_at,
                    :payment_method,
                    :delivery_method,
                    :delivery_status,
                    :source,
                    :total_ht,
                    :total_vat,
                    :total_ttc
                )'
            );

            $saleStmt->execute([
                'user_id' => $data['user_id'] ?? null,
                'client_id' => ($data['client_id'] ?? null) ?: null,

                'status' => $status,
                'payment_status' => $paymentStatus,
                'paid_at' => $paidAt,

                'payment_method' => $paymentMethod,
                'delivery_method' => $deliveryMethod,
                'delivery_status' => $deliveryStatus,
                'source' => $source,

                'total_ht' => $totalHt,
                'total_vat' => $totalVat,
                'total_ttc' => $totalTtc,
            ]);


            /*
             * Retrieve the ID of the newly created sale.
             *
             * Every sale_items row will reference this ID.
             */
            $saleId = (int)$this->db->lastInsertId();


            /*
             * Prepare the sale-item INSERT once and reuse it.
             */
            $itemStmt = $this->db->prepare(
                'INSERT INTO sale_items
                (
                    sale_id,
                    product_id,
                    quantity,
                    unit_price,
                    vat_rate,
                    total_ht,
                    total_vat,
                    total_ttc
                )
                VALUES
                (
                    :sale_id,
                    :product_id,
                    :quantity,
                    :unit_price,
                    :vat_rate,
                    :total_ht,
                    :total_vat,
                    :total_ttc
                )'
            );


            /*
             * Stock belongs to the payment lifecycle, not the order lifecycle.
             *
             * Paid sale:
             *     stock decreases immediately.
             *
             * Pending payment:
             *     stock stays unchanged.
             *
             * Website orders will later decrease stock when payment
             * changes from pending to paid.
             */
            $stockStmt = null;

            if ($paymentStatus === 'paid') {
                $stockStmt = $this->db->prepare(
                    'UPDATE products
                     SET stock = stock - :quantity
                     WHERE id = :id
                     AND stock >= :required_quantity'
                );
            }


            /*
             * Save every sale line.
             *
             * If the sale is already paid, also reduce its stock.
             */
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

                if ($stockStmt) {
                    $stockStmt->execute([
                        'quantity' => $item['quantity'],
                        'required_quantity' => $item['quantity'],
                        'id' => $item['product_id'],
                    ]);

                    /*
                     * Stock is checked atomically during the UPDATE.
                     *
                     * This protects against another sale consuming stock
                     * after our initial validation but before this update.
                     */
                    if ($stockStmt->rowCount() === 0) {
                        throw new Exception(
                            'Stock insuffisant lors de la mise à jour'
                        );
                    }
                }
            }


            /*
             * Every operation succeeded.
             * Permanently save all database changes.
             */
            $this->db->commit();

            return $saleId;

        } catch (Throwable $exception) {
            /*
             * Something failed.
             *
             * Cancel every database modification made during
             * this sale creation and let the controller handle the error.
             */
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM sales
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $sale = $stmt->fetch();

        return $sale ?: null;
    }

    /**
     * Sets a pending sale as paid and consumes its product stock.
     *
     * The operation is idempotent: if the sale is already paid,
     * it does nothing and returns successfully.
     *
     * @param int $saleId
     * @return bool
     * @throws Throwable
     */
    public function setAsPaid(int $saleId): bool
    {
        $this->db->beginTransaction();

        try {
            /*
             * Lock the sale row while payment is being processed.
             *
             * This prevents two simultaneous payment confirmations
             * from decreasing stock twice for the same sale.
             */
            $saleStmt = $this->db->prepare(
                'SELECT *
                 FROM sales
                 WHERE id = :id
                 FOR UPDATE'
            );

            $saleStmt->execute([
                'id' => $saleId,
            ]);

            $sale = $saleStmt->fetch();

            if (!$sale) {
                throw new Exception('Vente introuvable');
            }


            /*
             * Payment confirmation can sometimes arrive more than once
             * from a payment provider.
             *
             * If this sale is already paid, there is nothing more to do.
             */
            if ($sale['payment_status'] === 'paid') {
                $this->db->commit();

                return true;
            }


            /*
             * A cancelled sale must never be paid.
             */
            if ($sale['status'] === 'cancelled') {
                throw new Exception(
                    'Impossible de payer une vente annulée'
                );
            }


            /*
             * Retrieve the products and quantities belonging to this sale.
             *
             * sale_items already contains the quantities validated
             * when the order was originally created.
             */
            $itemsStmt = $this->db->prepare(
                'SELECT product_id, quantity
                 FROM sale_items
                 WHERE sale_id = :sale_id'
            );

            $itemsStmt->execute([
                'sale_id' => $saleId,
            ]);

            $saleItems = $itemsStmt->fetchAll();

            if (empty($saleItems)) {
                throw new Exception(
                    'Aucun produit dans la vente'
                );
            }


            /*
             * Prepare one reusable stock update statement.
             *
             * The stock condition is checked directly inside MySQL.
             * This protects against another order consuming the stock
             * immediately before this payment is confirmed.
             */
            $stockStmt = $this->db->prepare(
                'UPDATE products
                 SET stock = stock - :quantity
                 WHERE id = :product_id
                 AND stock >= :required_quantity'
            );


            /*
             * Consume stock for every product in the paid sale.
             */
            foreach ($saleItems as $item) {
                $stockStmt->execute([
                    'quantity' => $item['quantity'],
                    'required_quantity' => $item['quantity'],
                    'product_id' => $item['product_id'],
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new Exception(
                        'Stock insuffisant lors du paiement'
                    );
                }
            }


            /*
             * Payment succeeded.
             *
             * The order now enters preparation while delivery remains
             * independently managed through delivery_status.
             */
            $updateSaleStmt = $this->db->prepare(
                'UPDATE sales
                 SET
                    payment_status = :payment_status,
                    paid_at = NOW(),
                    status = :status
                 WHERE id = :id'
            );

            $updateSaleStmt->execute([
                'payment_status' => 'paid',
                'status' => 'preparing',
                'id' => $saleId,
            ]);


            /*
             * Stock and payment status were both updated successfully.
             */
            $this->db->commit();

            return true;

        } catch (Throwable $exception) {
            /*
             * If anything fails, restore both stock and sale state
             * to exactly what they were before this operation.
             */
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @param int $saleId
     * @param string $deliveryStatus
     * @return bool
     * @throws Throwable
     */
    public function setDeliveryStatus(int $saleId, string $deliveryStatus ): bool
    {
        $this->db->beginTransaction();

        try {
            /*
             * Lock the sale while its delivery state is being modified.
             */
            $stmt = $this->db->prepare(
                'SELECT
                    id,
                    status,
                    payment_status,
                    delivery_method,
                    delivery_status
                 FROM sales
                 WHERE id = :id
                 FOR UPDATE'
            );

            $stmt->execute([
                'id' => $saleId,
            ]);

            $sale = $stmt->fetch();

            if (!$sale) {
                throw new Exception('Vente introuvable');
            }


            /*
             * Finished or cancelled orders cannot move anymore.
             */
            if (
                in_array(
                    $sale['status'],
                    ['completed', 'cancelled'],
                    true
                )
            ) {
                throw new Exception(
                    'Cette vente ne peut plus être modifiée'
                );
            }


            /*
             * Delivery starts only after payment has been confirmed.
             */
            if ($sale['payment_status'] !== 'paid') {
                throw new Exception(
                    'Le paiement doit être confirmé avant la livraison'
                );
            }


            /*
             * Get the only valid next delivery state.
             *
             * Livraison:
             * pending → shipped → delivered
             *
             * Retrait magasin:
             * pending → ready_for_pickup → collected
             */
            $deliveryMethod = $sale['delivery_method'];
            $currentStatus = $sale['delivery_status'];

            $nextStatus =
                self::DELIVERY_TRANSITIONS_BY_METHOD[$deliveryMethod][$currentStatus]
                ?? null;

            if ($nextStatus !== $deliveryStatus) {
                throw new Exception(
                    'Transition de livraison invalide'
                );
            }

            /*
             * Save the new delivery state.
             */
            $updateStmt = $this->db->prepare(
                'UPDATE sales
                 SET delivery_status = :delivery_status
                 WHERE id = :id'
            );

            $updateStmt->execute([
                'delivery_status' => $deliveryStatus,
                'id' => $saleId,
            ]);

            $this->db->commit();

            return true;

        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @param int $saleId
     * @return bool
     * @throws Throwable
     */
    public function setAsCompleted(int $saleId): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT
                    id,
                    status,
                    payment_status,
                    delivery_method,
                    delivery_status
                 FROM sales
                 WHERE id = :id
                 FOR UPDATE'
            );

            $stmt->execute([
                'id' => $saleId,
            ]);

            $sale = $stmt->fetch();

            if (!$sale) {
                throw new Exception('Vente introuvable');
            }


            /*
             * Already completed: nothing more to do.
             */
            if ($sale['status'] === 'completed') {
                $this->db->commit();

                return true;
            }

            if ($sale['status'] === 'cancelled') {
                throw new Exception(
                    'Une vente annulée ne peut pas être terminée'
                );
            }


            /*
             * An order can only be completed after payment.
             */
            if ($sale['payment_status'] !== 'paid') {
                throw new Exception(
                    'La vente doit être payée avant d’être terminée'
                );
            }


            /*
             * Delivery must also be finished.
             */
            $expectedDeliveryStatus =
                $sale['delivery_method'] === 'livraison'
                    ? 'delivered'
                    : 'collected';

            if (
                $sale['delivery_status']
                !== $expectedDeliveryStatus
            ) {
                throw new Exception(
                    'La livraison ou le retrait n’est pas terminé'
                );
            }


            $updateStmt = $this->db->prepare(
                'UPDATE sales
                 SET status = :status
                 WHERE id = :id'
            );

            $updateStmt->execute([
                'status' => 'completed',
                'id' => $saleId,
            ]);

            $this->db->commit();

            return true;

        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @param int $saleId
     * @return bool
     * @throws Throwable
     */
    public function setAsCancelled(int $saleId): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT
                    id,
                    status,
                    payment_status
                 FROM sales
                 WHERE id = :id
                 FOR UPDATE'
            );

            $stmt->execute([
                'id' => $saleId,
            ]);

            $sale = $stmt->fetch();

            if (!$sale) {
                throw new Exception('Vente introuvable');
            }

            if ($sale['status'] === 'cancelled') {
                $this->db->commit();

                return true;
            }

            if ($sale['status'] === 'completed') {
                throw new Exception(
                    'Une vente terminée ne peut pas être annulée'
                );
            }

            /*
             * Paid orders need a refund workflow.
             *
             * We do not cancel them here because their stock has already
             * been removed.
             */
            if ($sale['payment_status'] === 'paid') {
                throw new Exception(
                    'Une vente payée doit être remboursée avant annulation'
                );
            }


            $updateStmt = $this->db->prepare(
                'UPDATE sales
                 SET status = :status
                 WHERE id = :id'
            );

            $updateStmt->execute([
                'status' => 'cancelled',
                'id' => $saleId,
            ]);

            $this->db->commit();

            return true;

        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }
}