<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use JsonException;
use RuntimeException;
use Throwable;

/**
 * PageCheckoutController class
 */
class PageCheckoutController extends Controller
{
    private const PAYMENT_METHODS = [
        'cb',
        'virement',
        'especes',
        'cheque',
    ];

    private const DELIVERY_METHODS = [
        'livraison',
        'magasin',
    ];

    /**
     * Displays the checkout page.
     * @return void
     * @throws JsonException
     */
    public function index(): void
    {
        // Read cart items.
        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        if (!is_array($items) || empty($items)) {
            header(
                'Location: /public/index.php?route=/cart'
            );
            exit;
        }

        // Rebuild checkout from database data.
        try {
            $checkout = $this->buildCheckout($items);
        } catch (RuntimeException) {
            header(
                'Location: /public/index.php?route=/cart'
            );
            exit;
        }


        // Load logged-in client.
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


        // Null means guest checkout.
        $checkout['client'] = $client;

        $this->view(
            'frontend/checkout/index',
            $checkout
        );
    }

    /**
     * Confirms the checkout process and displays the checkout summary.
     *
     * @return void
     * @throws Throwable
     */
    public function confirm(): void
    {
        $paymentMethod =
            $_POST['payment_method'] ?? '';

        $deliveryMethod =
            $_POST['delivery_method'] ?? '';

        $address =
            trim($_POST['address'] ?? '');

        $postalCode =
            trim($_POST['postal_code'] ?? '');

        $city =
            trim($_POST['city'] ?? '');

        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );


        // Validate cart.
        if (!is_array($items) || empty($items)) {
            throw new RuntimeException(
                'Panier invalide.'
            );
        }


        // Validate payment method.
        if (
            !in_array(
                $paymentMethod,
                self::PAYMENT_METHODS,
                true
            )
        ) {
            throw new RuntimeException(
                'Mode de paiement invalide.'
            );
        }


        // Validate delivery method.
        if (
            !in_array(
                $deliveryMethod,
                self::DELIVERY_METHODS,
                true
            )
        ) {
            throw new RuntimeException(
                'Mode de livraison invalide.'
            );
        }


        // Delivery requires an address.
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


        // Cash and cheque require store pickup.
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


        // Build delivery snapshot.
        $deliveryAddress = null;

        if ($deliveryMethod === 'livraison') {
            $deliveryAddress = [
                'address' => $address,
                'postal_code' => $postalCode,
                'city' => $city,
            ];
        }


        // Validate trusted product data again.
        $this->buildCheckout(
            $items,
            strict: true
        );


        // Resolve the client.
        $clientModel = new Client();
        $clientId = null;

        if (isset($_SESSION['client']['id'])) {
            $client = $clientModel->find(
                (int) $_SESSION['client']['id']
            );

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

        } else {
            $firstname =
                trim($_POST['firstname'] ?? '');

            $lastname =
                trim($_POST['lastname'] ?? '');

            $email =
                trim($_POST['email'] ?? '');

            $phone =
                trim($_POST['phone'] ?? '');


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

            if (
                !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                throw new RuntimeException(
                    'Adresse e-mail invalide.'
                );
            }


            $client = $clientModel->findByEmail(
                $email
            );


            // Existing account must authenticate.
            if ($client) {
                if (!empty($client['password'])) {
                    throw new RuntimeException(
                        'Un compte existe déjà avec cette adresse e-mail. '
                        . 'Veuillez vous connecter.'
                    );
                }

                $clientId = (int)$client['id'];

            } else {
                // Create guest website client.
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

        // Create website order.
        $saleModel = new Sale();

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


        // Protect success page access.
        $_SESSION['checkout_sale_id'] = $saleId;


        header(
            'Location: /public/index.php?route=/checkout/success&sale_id='
            . $saleId
        );

        exit;
    }

    /**
     * @param array $items
     * @param bool $strict
     * @return array
     */
    private function buildCheckout( array $items, bool $strict = false ): array
    {
        $productIds = [];


        // Collect product IDs.
        foreach ($items as $item) {
            $productId =
                (int) ($item['product_id'] ?? 0);

            if ($productId > 0) {
                $productIds[] = $productId;
            }
        }


        // Remove duplicates and reset keys.
        $productIds = array_values(
            array_unique($productIds)
        );

        if (empty($productIds)) {
            throw new RuntimeException(
                'Panier invalide.'
            );
        }


        // Load products in one query.
        $productModel = new Product();

        $products =
            $productModel->getActiveProductsByIds(
                $productIds
            );


        // Index products by ID.
        $productsById = [];

        foreach ($products as $product) {
            $productsById[
                (int) $product['id']
            ] = $product;
        }


        $checkoutItems = [];

        $checkoutTotalHt = 0.0;
        $checkoutTotalVat = 0.0;
        $checkoutTotalTtc = 0.0;


        foreach ($items as $item) {
            $productId =
                (int) ($item['product_id'] ?? 0);

            $quantity =
                (float) ($item['quantity'] ?? 0);


            // Validate basic item data.
            if (
                $productId <= 0
                || $quantity <= 0
            ) {
                if ($strict) {
                    throw new RuntimeException(
                        'Produit ou quantité invalide.'
                    );
                }

                continue;
            }


            // Use trusted product data.
            $product =
                $productsById[$productId]
                ?? null;

            if (!$product) {
                if ($strict) {
                    throw new RuntimeException(
                        'Un produit du panier n’est plus disponible.'
                    );
                }

                continue;
            }


            // Unit products require integers.
            if (
                $product['sale_type'] === 'unite'
                && floor($quantity) !== $quantity
            ) {
                if ($strict) {
                    throw new RuntimeException(
                        'Quantité invalide pour le produit : '
                        . $product['name']
                    );
                }

                $quantity = floor($quantity);
            }


            // Weighted products use configured steps.
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
                    floor(
                        $quantity
                        / PRODUCT_WEIGHT_STEP_GRAMS
                    )
                    * PRODUCT_WEIGHT_STEP_GRAMS;
            }


            // Check current stock.
            $stock =
                (float) $product['stock'];

            if ($quantity > $stock) {
                if ($strict) {
                    throw new RuntimeException(
                        'Stock insuffisant pour le produit : '
                        . $product['name']
                    );
                }

                if (
                    $product['sale_type']
                    === 'poids'
                ) {
                    $quantity =
                        floor(
                            $stock
                            / PRODUCT_WEIGHT_STEP_GRAMS
                        )
                        * PRODUCT_WEIGHT_STEP_GRAMS;
                } else {
                    $quantity = floor($stock);
                }
            }

            if ($quantity <= 0) {
                continue;
            }


            // Calculate HT, VAT and TTC.
            $unitPrice =
                (float) $product['price'];

            $vatRate =
                (float) $product['vat_rate'];

            $quantityForPrice =
                $product['sale_type'] === 'poids'
                    ? $quantity / GRAMS_PER_KILOGRAM
                    : $quantity;

            $lineTotalHt =
                $unitPrice * $quantityForPrice;

            $lineTotalVat =
                $lineTotalHt
                * ($vatRate / 100);

            $lineTotalTtc =
                $lineTotalHt
                + $lineTotalVat;


            // Add checkout line.
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
        $saleId = (int) ($_GET['sale_id'] ?? 0);

        $sessionSaleId = (int) ($_SESSION['checkout_sale_id'] ?? 0 );


        // Validate success page access.
        if (
            $saleId <= 0
            || $sessionSaleId <= 0
            || $saleId !== $sessionSaleId
        ) {
            header(
                'Location: /public/index.php?route=/products'
            );
            exit;
        }


        $saleModel = new Sale();

        $sale = $saleModel->findById(
            $saleId
        );

        if (!$sale) {
            header(
                'Location: /public/index.php?route=/products'
            );
            exit;
        }


        $this->view(
            'frontend/checkout/success',
            [
                'sale_id' => $saleId,
            ]
        );
    }
}