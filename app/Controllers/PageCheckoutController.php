<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use RuntimeException;
use Throwable;

/**
 * PageCheckoutController class
 */
class PageCheckoutController extends Controller
{

    /**
     * Displays the checkout page.
     * @return void
     */
    public function index(): void
    {
        // Read cart items.
        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        if (!is_array($items) || empty($items)) {
            header('Location: /public/index.php?route=/cart');
            exit;
        }

        // Rebuild checkout from database data.
        try {
            $checkout = $this->buildCheckout($items);
        } catch (RuntimeException $exception) {
            header('Location: /public/index.php?route=/cart');
            exit;
        }

        // Load the logged-in client when available.
        $client = null;

        if (isset($_SESSION['client']['id'])) {
            $clientModel = new Client();

            $client = $clientModel->find(
                (int) $_SESSION['client']['id']
            );

            if (
                !$client
                || (int) $client['is_active'] !== 1
            ) {
                unset($_SESSION['client']);
                $client = null;
            }
        }

        // Client is null for guest checkout.
        $checkout['client'] = $client;

        $this->view(
            'frontend/checkout/index',
            $checkout
        );
    }

    /**
     * Confirms the checkout process and displays the checkout summary.
     * @return void
     * @throws Throwable
     */
    public function confirm(): void
    {
        $paymentMethod = $_POST['payment_method'] ?? '';
        $deliveryMethod = $_POST['delivery_method'] ?? '';

        $address = trim($_POST['address'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $city = trim($_POST['city'] ?? '');

        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        // Validate cart.
        if (!is_array($items) || empty($items)) {
            throw new RuntimeException('Panier invalide.');
        }

        $allowedPaymentMethods = [
            'cb',
            'virement',
            'especes',
            'cheque',
        ];

        $allowedDeliveryMethods = [
            'livraison',
            'magasin',
        ];

        // Validate payment and delivery.
        if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
            throw new RuntimeException('Mode de paiement invalide.');
        }

        if (!in_array($deliveryMethod, $allowedDeliveryMethods, true)) {
            throw new RuntimeException('Mode de livraison invalide.');
        }

        // A delivery address is required only for home delivery.
        if (
            $deliveryMethod === 'livraison'
            && (
                $address === ''
                || $postalCode === ''
                || $city === ''
            )
        ) {
            throw new RuntimeException(
                'Adresse de livraison incomplète.'
            );
        }

        // Cash and cheque are available only for store pickup.
        if (
            $deliveryMethod === 'livraison'
            && in_array(
                $paymentMethod,
                ['especes', 'cheque'],
                true
            )
        ) {
            throw new RuntimeException(
                'Ce mode de paiement n’est pas disponible pour la livraison.'
            );
        }

        // Build the delivery address snapshot.
        $deliveryAddress = null;

        if ($deliveryMethod === 'livraison') {
            $deliveryAddress = [
                'address' => $address,
                'postal_code' => $postalCode,
                'city' => $city,
            ];
        }

        // Rebuild and validate the checkout from trusted database data.
        // Product data it's loaded again from MySQL.
        try {
            $checkout = $this->buildCheckout(
                $items,
                strict: true
            );
        } catch (\RuntimeException $exception) {
            die(
            htmlspecialchars(
                $exception->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            )
            );
        }

        $clientModel = new Client();
        $clientId = null;

        if (isset($_SESSION['client']['id'])) {
            $client = $clientModel->find(
                (int) $_SESSION['client']['id']
            );

            // Validate authenticated client.
            if (
                !$client
                || (int) $client['is_active'] !== 1
            ) {
                unset($_SESSION['client']);

                header(
                    'Location: /public/index.php?route=/login'
                );
                exit;
            }

            $clientId = (int) $client['id'];

            // GUEST CLIENT
        } else {
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // Validate guest identity.
            if (
                $firstname === ''
                || $lastname === ''
                || $email === ''
            ) {
                throw new RuntimeException(
                    'Champs obligatoires manquants.'
                );
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException(
                    'Adresse e-mail invalide.'
                );
            }

            $client = $clientModel->findByEmail($email);

            if ($client) {

                // Existing online account must authenticate.
                if (!empty($client['password'])) {
                    throw new RuntimeException(
                        'Un compte existe déjà avec cette adresse e-mail. '
                        . 'Veuillez vous connecter.'
                    );
                }

                $clientId = (int) $client['id'];

            } else {

                // Create a website client without an account password.
                $clientId = $clientModel->create([
                    'name' => $firstname . ' ' . $lastname,
                    'email' => $email,
                    'phone' => $phone ?: null,
                    'address' => $deliveryAddress,
                    'source' => 'website',
                    'is_active' => 1,
                ]);
            }
        }

        // Create a WEBSITE order.
        $saleModel = new Sale();
        try {
            $saleId = $saleModel->create([
                'user_id' => null,
                'client_id' => $clientId,

                'items' => $items,

                'delivery_method' => $deliveryMethod,
                'delivery_address' => $deliveryAddress,
                'payment_method' => $paymentMethod,

                'source' => 'website',

                'status' => 'pending',
                'payment_status' => 'pending',
                'delivery_status' => 'pending',
            ]);

            // Save sale ID in session to validate the success page access.
            // The success page uses this to prevent somebody from simply changing the sale_id in the URL.
            $_SESSION['checkout_sale_id'] = $saleId;

        } catch (Throwable $exception) {
            throw new RuntimeException(
                $exception->getMessage()
            );
        }

        // Redirect only after the sale transaction was successfully committed.
        // The React success component will clear localStorage afterwards.
        header(
            'Location: /public/index.php?route=/checkout/success&sale_id='
            . $saleId
        );

        exit;
    }

    /**
     * Processes a list of items to build a checkout summary, including item details and total cost.
     *
     * @param array $items The list of items to process. Each item should include a 'product_id' and 'quantity'.
     * @param bool $strict Whether to enforce strict validation for product availability and stock.
     *
     * @return array An array containing:
     *               - 'checkout_items' (array): A list of valid items including product details, quantities, and line totals.
     *               - 'checkout_total' (float): The total cost of the items in the checkout.
     *
     * @throws RuntimeException If the cart is invalid, has no valid products, or fails strict validation checks.
     */
    private function buildCheckout( array $items, bool $strict = false ): array
    {
    // Collect product IDs.
        $productIds = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);

            if ($productId > 0) {
                $productIds[] = $productId;
            }
        }

        $productIds = array_unique($productIds);

        if (empty($productIds)) {
            throw new RuntimeException(
                'Panier invalide.'
            );
        }


        // Load products in one query.
        $productModel = new Product();

        $products = $productModel->getActiveProductsByIds(
            $productIds
        );


        // Index products by ID.
        $productsById = [];

        foreach ($products as $product) {
            $productsById[(int) $product['id']] = $product;
        }

        // Build checkout totals.
        $checkoutItems = [];

        $checkoutTotalHt = 0.0;
        $checkoutTotalVat = 0.0;
        $checkoutTotalTtc = 0.0;


        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                if ($strict) {
                    throw new RuntimeException(
                        'Produit ou quantité invalide.'
                    );
                }

                continue;
            }

            // Find trusted product data.
            $product = $productsById[$productId] ?? null;

            if (!$product) {
                if ($strict) {
                    throw new RuntimeException(
                        'Un produit du panier n’est plus disponible.'
                    );
                }

                continue;
            }

            // Unit products require whole quantities.
            if (
                $product['sale_type'] === 'poids'
                && fmod(
                    $quantity,
                    (float) PRODUCT_WEIGHT_STEP_GRAMS
                ) !== 0.0
            ) {
                if ($strict) {
                    throw new RuntimeException(
                        'Poids invalide pour le produit : '
                        . $product['name']
                    );
                }

                $quantity =
                    floor($quantity / PRODUCT_WEIGHT_STEP_GRAMS)
                    * PRODUCT_WEIGHT_STEP_GRAMS;
            }

            // Check stock.
            $stock = (float) $product['stock'];

            if ($quantity > $stock) {
                if ($strict) {
                    throw new RuntimeException(
                        'Stock insuffisant pour le produit : '
                        . $product['name']
                    );
                }

                $quantity = $stock;
            }

            if ($quantity <= 0) {
                continue;
            }

            // Calculate HT, VAT and TTC.
            $unitPrice = (float) $product['price'];
            $vatRate = (float) $product['vat_rate'];

            $quantityForPrice = $product['sale_type'] === 'poids' ? $quantity / GRAMS_PER_KILOGRAM : $quantity;

            $lineTotalHt = $unitPrice * $quantityForPrice;
            $lineTotalVat = $lineTotalHt * ($vatRate / 100);
            $lineTotalTtc = $lineTotalHt + $lineTotalVat;

            // Add checkout item.
            $checkoutItems[] = [
                'checkout_product' => $product,
                'checkout_quantity' => $quantity,
                'checkout_line_total_ht' => $lineTotalHt,
                'checkout_line_total_vat' => $lineTotalVat,
                'checkout_line_total_ttc' => $lineTotalTtc,
            ];

            $checkoutTotalHt += $lineTotalHt;
            $checkoutTotalVat += $lineTotalVat;
            $checkoutTotalTtc += $lineTotalTtc;
        }

        // Require at least one valid item.
        if (empty($checkoutItems)) {
            throw new RuntimeException(
                'Aucun produit valide dans le panier.'
            );
        }

        return [
            'checkout_items' => $checkoutItems,
            'checkout_total_ht' => $checkoutTotalHt,
            'checkout_total_vat' => $checkoutTotalVat,
            'checkout_total_ttc' => $checkoutTotalTtc,
        ];
    }

    /**
     * @return void
     */
    public function success(): void
    {
        $saleId = (int)($_GET['sale_id'] ?? 0);
        $sessionSaleId = (int)($_SESSION['checkout_sale_id'] ?? 0);

        if (
            $saleId <= 0
            || $sessionSaleId <= 0
            || $saleId !== $sessionSaleId
        ) {
            header('Location: /public/index.php?route=/products');
            exit;
        }

        $saleModel = new Sale();

        $sale = $saleModel->findById($saleId);

        if (!$sale) {
            header('Location: /public/index.php?route=/products');
            exit;
        }

        $this->view('frontend/checkout/success', [
            'sale_id' => $saleId,
        ]);
    }
}