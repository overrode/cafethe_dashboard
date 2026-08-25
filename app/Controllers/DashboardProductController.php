<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use JetBrains\PhpStorm\NoReturn;
use Throwable;
use App\Core\Auth;

/**
 * Handles product-related operations such as displaying products,
 * rendering creation forms, and storing new product data.
 */
class DashboardProductController extends Controller
{
    /**
     * @var array<string>
     */
    private const SALE_TYPES = [
        'poids',
        'unite',
    ];
    /**
     * Handles the display of the product index page.
     *
     * @return void
     */
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->all();
        $categories = $productModel->getCategories();

        // Data needed by the React products page.
        $this->view('backend/products/index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $productModel = new Product();
        $categories = $productModel->getCategories();

        $this->view('backend/products/create', [
            'categories' => $categories,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function store(): void
    {
        $productModel = new Product();

        $productModel->create([
            'category_id' => $_POST['category_id'],
            'sku' => trim($_POST['sku']),
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'sale_type' => $_POST['sale_type'],
            'price' => $_POST['price'],
            'vat_rate' => $_POST['vat_rate'],
            'stock' => $_POST['stock'],
            'image' => trim($_POST['image'] ?? ''),
            'origin' => trim($_POST['origin'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        header('Location: /public/index.php?route=/products');
        exit;
    }

    /**
     * @return void
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        $productModel = new Product();
        $product = $productModel->find($id);
        $categories = $productModel->getCategories();

        if (!$product) {
            echo 'Produit introuvable';
            return;
        }

        $this->view('backend/products/edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        $productModel = new Product();

        $productModel->update($id, [
            'category_id' => $_POST['category_id'],
            'sku' => trim($_POST['sku']),
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'sale_type' => $_POST['sale_type'],
            'price' => $_POST['price'],
            'vat_rate' => $_POST['vat_rate'],
            'stock' => $_POST['stock'],
            'image' => trim($_POST['image'] ?? ''),
            'origin' => trim($_POST['origin'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        header('Location: /public/index.php?route=/products');
        exit;
    }

    /**
     * Marks a product as inactive based on the provided ID and redirects to the products page.
     *
     * @return void
     */
    #[NoReturn]
    public function deactivate(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0) {
            $productModel = new Product();
            $productModel->deactivate($id);
        }

        header('Location: /public/index.php?route=/products');
        exit;
    }

    /**
     * Reads product data from the request body and creates a new product.
     *
     * @return void
     */
    public function storeJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Dashboard authentication.
        if (!Auth::id()) {
            $this->jsonError('Utilisateur non authentifié.', 401);
            return;
        }

        // Read submitted product values.
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $sku = trim($_POST['sku'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $saleType = trim($_POST['sale_type'] ?? '');
        $price = $_POST['price'] ?? '';
        $vatRate = $_POST['vat_rate'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $image = trim($_POST['image'] ?? '');
        $origin = trim($_POST['origin'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        // Required product fields.
        if (
            $categoryId <= 0
            || $sku === ''
            || $name === ''
            || $saleType === ''
        ) {
            $this->jsonError(
                'Veuillez remplir les champs obligatoires.',
                422
            );

            return;
        }

        // Validate numeric values.
        if (
            !is_numeric($price)
            || !is_numeric($vatRate)
            || !is_numeric($stock)
            || (float)$price < 0
            || (float)$vatRate < 0
            || (float)$vatRate > 100
            || (float)$stock < 0
        ) {
            $this->jsonError(
                'Prix, TVA ou stock invalide.',
                422
            );

            return;
        }

        // Allow only supported sale types.
        if (!in_array($saleType, self::SALE_TYPES, true)) {
            $this->jsonError(
                'Type de vente invalide.',
                422
            );

            return;
        }

        $productModel = new Product();

        // SKU must be unique.
        if ($productModel->findBySku($sku)) {
            $this->jsonError(
                'Un produit avec ce SKU existe déjà.',
                409
            );

            return;
        }

        try {
            // Save the product.
            $productId = $productModel->create([
                'category_id' => $categoryId,
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'sale_type' => $saleType,
                'price' => (float)$price,
                'vat_rate' => (float)$vatRate,
                'stock' => (float)$stock,
                'image' => $image ?: null,
                'origin' => $origin ?: null,
                'is_active' => $isActive ? 1 : 0,
            ]);

            // Return the complete product to React.
            $product = $productModel->findWithCategory(
                $productId
            );

            echo json_encode([
                'success' => true,
                'product' => $product,
            ]);

        } catch (Throwable $exception) {
            $this->jsonError(
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Reads product data from the request body and updates the corresponding product.
     *
     * @return void
     */
    public function updateJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Dashboard authentication.
        if (!Auth::id()) {
            $this->jsonError('Utilisateur non authentifié.', 401);
            return;
        }

        // Read submitted product values.
        $id = (int)($_POST['id'] ?? 0);
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $sku = trim($_POST['sku'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $saleType = trim($_POST['sale_type'] ?? '');
        $price = $_POST['price'] ?? '';
        $vatRate = $_POST['vat_rate'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $image = trim($_POST['image'] ?? '');
        $origin = trim($_POST['origin'] ?? '');
        $isActive = ($_POST['is_active'] ?? '1') === '1';

        // Required product fields.
        if (
            $id <= 0
            || $categoryId <= 0
            || $sku === ''
            || $name === ''
            || $saleType === ''
        ) {
            $this->jsonError(
                'Données produit invalides.',
                422
            );

            return;
        }

        // Validate numeric values.
        if (
            !is_numeric($price)
            || !is_numeric($vatRate)
            || !is_numeric($stock)
            || (float)$price < 0
            || (float)$vatRate < 0
            || (float)$vatRate > 100
            || (float)$stock < 0
        ) {
            $this->jsonError(
                'Prix, TVA ou stock invalide.',
                422
            );

            return;
        }

        // Allow only supported sale types.
        if (!in_array($saleType, self::SALE_TYPES, true)) {
            $this->jsonError(
                'Type de vente invalide.',
                422
            );

            return;
        }

        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            $this->jsonError(
                'Produit introuvable.',
                404
            );

            return;
        }

        // Allow the same SKU only for the current product.
        $existingProduct = $productModel->findBySku($sku);

        if (
            $existingProduct
            && (int)$existingProduct['id'] !== $id
        ) {
            $this->jsonError(
                'Un produit avec ce SKU existe déjà.',
                409
            );

            return;
        }

        try {
            // Update the product.
            $productModel->update($id, [
                'category_id' => $categoryId,
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'sale_type' => $saleType,
                'price' => (float)$price,
                'vat_rate' => (float)$vatRate,
                'stock' => (float)$stock,
                'image' => $image,
                'origin' => $origin,
                'is_active' => $isActive ? 1 : 0,
            ]);

            // Return updated data to React.
            echo json_encode([
                'success' => true,
                'product' => $productModel->findWithCategory($id),
            ]);

        } catch (Throwable $exception) {
            $this->jsonError(
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Toggles the availability status of a product.
     *
     * @return void
     */
    public function setActiveJson(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Dashboard authentication.
        if (!Auth::id()) {
            $this->jsonError('Utilisateur non authentifié.', 401);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $isActive = ($_POST['is_active'] ?? '') === '1';

        if ($id <= 0) {
            $this->jsonError(
                'Produit invalide.',
                422
            );

            return;
        }

        $productModel = new Product();

        if (!$productModel->find($id)) {
            $this->jsonError(
                'Produit introuvable.',
                404
            );

            return;
        }

        try {
            // Toggle product availability.
            $productModel->setActive(
                $id,
                $isActive
            );

            echo json_encode([
                'success' => true,
                'id' => $id,
                'is_active' => $isActive ? 1 : 0,
            ]);

        } catch (Throwable $exception) {
            $this->jsonError(
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Standard JSON error response.
     *
     * @param string $message
     * @param int $status
     * @return void
     */
    private function jsonError(
        string $message,
        int    $status
    ): void
    {
        http_response_code($status);

        echo json_encode([
            'success' => false,
            'error' => $message,
        ]);
    }
}
