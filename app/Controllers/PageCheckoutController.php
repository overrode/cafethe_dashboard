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
        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        if (!is_array($items) || empty($items)) {
            header('Location: /public/index.php?route=/cart');
            exit;
        }

        try {
            $checkout = $this->buildCheckout($items);
        } catch (RuntimeException $exception) {
            header('Location: /public/index.php?route=/cart');
            exit;
        }

        $this->view(
            'frontend/checkout/index',
            $checkout
        );
    }

    /**
     * Confirms the checkout process and displays the checkout summary.
     * @return void
     */
    public function confirm(): void
    {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $address = trim($_POST['address'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $city = trim($_POST['city'] ?? '');

        $paymentMethod = $_POST['payment_method'] ?? '';
        $deliveryMethod = $_POST['delivery_method'] ?? '';

        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        if (
            $firstname === ''
            || $lastname === ''
            || $email === ''
        ) {
            die('Champs obligatoires manquants.');
        }

        if (
            $deliveryMethod === 'livraison'
            && (
                $address === ''
                || $postalCode === ''
                || $city === ''
            )
        ) {
            die('Adresse de livraison incomplète.');
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die('Adresse e-mail invalide.');
        }

        if (!is_array($items) || empty($items)) {
            die('Panier invalide.');
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

        if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
            die('Mode de paiement invalide.');
        }

        if (!in_array($deliveryMethod, $allowedDeliveryMethods, true)) {
            die('Mode de livraison invalide.');
        }

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

        //TODO: refactor Cusomer / Client data
        $customer = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
        ];

        $delivery = [
            'address' => $address,
            'postal_code' => $postalCode,
            'city' => $city,
        ];

        $fullAddress = null;
        if ($deliveryMethod === 'livraison') {
            $fullAddress = sprintf(
                '%s, %s %s',
                $address,
                $postalCode,
                $city
            );
        }

        $clientModel = new Client();
        $client = $clientModel->findByEmail($email);

        echo "CLIENT EMAIL: ";
        echo "<pre>";
        print_r($client);
        echo "</pre>";
        //die();


        //TODO: if the email exists, what are we doing with the new form data? (name, addres...)
        if ($client) {
            $clientId = (int) $client['id'];
        } else {
            $clientId = $clientModel->create([
                'name' => $firstname . ' ' . $lastname,
                'email' => $email,
                'phone' => $phone,
                'address' => $fullAddress,
            ]);
        }

        $saleModel = new Sale();

        try {
            $saleId = $saleModel->create([
                'user_id' => null,
                'client_id' => $clientId,

                'items' => $items,

                'delivery_method' => $deliveryMethod,
                'payment_method' => $paymentMethod,

                'source' => 'website',

                'status' => 'pending',
                'payment_status' => 'pending',
                'delivery_status' => 'pending',

            ]);

            $_SESSION['checkout_sale_id'] = $saleId;

        } catch (Throwable $exception) {
            die(
                htmlspecialchars(
                    $exception->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }

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
    private function buildCheckout(array $items, bool $strict = false): array
    {
        $productIds = [];

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);

            if ($productId > 0) {
                $productIds[] = $productId;
            }
        }

        $productIds = array_unique($productIds);

        if (empty($productIds)) {
            throw new RuntimeException('Panier invalide.');
        }

        $productModel = new Product();

        $products = $productModel->getActiveProductsByIds($productIds);

        $productsById = [];

        foreach ($products as $product) {
            $productsById[(int)$product['id']] = $product;
        }

        $checkoutItems = [];
        $checkoutTotal = 0;

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                if ($strict) {
                    throw new RuntimeException(
                        'Produit ou quantité invalide.'
                    );
                }

                continue;
            }

            $product = $productsById[$productId] ?? null;

            if (!$product) {
                if ($strict) {
                    throw new RuntimeException(
                        'Un produit du panier n’est plus disponible.'
                    );
                }

                continue;
            }

            $stock = (int)$product['stock'];

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

            $lineTotal = (float)$product['price'] * $quantity;

            $checkoutItems[] = [
                'checkout_product' => $product,
                'checkout_quantity' => $quantity,
                'checkout_line_total' => $lineTotal,
            ];

            $checkoutTotal += $lineTotal;
        }

        if (empty($checkoutItems)) {
            throw new RuntimeException(
                'Aucun produit valide dans le panier.'
            );
        }

        return [
            'checkout_items' => $checkoutItems,
            'checkout_total' => $checkoutTotal,
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