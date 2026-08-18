<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use RuntimeException;
use \App\Models\Client;

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

        $items = json_decode(
            $_POST['items'] ?? '[]',
            true
        );

        if (
            $firstname === ''
            || $lastname === ''
            || $email === ''
            || $address === ''
            || $postalCode === ''
            || $city === ''
        ) {
            die('Champs obligatoires manquants.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die('Adresse e-mail invalide.');
        }

        if (!is_array($items) || empty($items)) {
            die('Panier invalide.');
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

        $fullAddress = sprintf(
            '%s, %s %s',
            $address,
            $postalCode,
            $city
        );

        $clientModel = new Client();
        $client = $clientModel->findByEmail($email);
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

        echo '<pre>';
        echo 'Client ID: ' . $clientId . '<br>';
        print_r([
            'customer' => $customer,
            'delivery' => $delivery,
            'checkout_items' => $checkout['checkout_items'],
            'checkout_total' => $checkout['checkout_total'],
        ]);

        echo '</pre>';
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
}