<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use Throwable;
use App\Core\Auth;
use App\Core\Logger;
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
            $this->jsonException(
                $exception,
                __FUNCTION__
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
            $this->jsonException(
                $exception,
                __FUNCTION__
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
            $this->jsonException(
                $exception,
                __FUNCTION__
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

        echo json_encode(
            [
            'success' => false,
            'error' => $message,
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Log unexpected JSON errors.
     *
     * @param Throwable $exception
     * @param string $action
     * @return void
     */
    private function jsonException(
        Throwable $exception,
        string $action
    ): void {
        Logger::exception(
            $exception,
            [
                'controller' => self::class,
                'action' => $action,
                'user_id' => Auth::id(),
            ]
        );

        $this->jsonError(
            IS_DEVELOPMENT
                ? $exception->getMessage()
                : 'Une erreur est survenue.',
            500
        );
    }

    /**
     * Read product form values.
     *
     * @return array
     */
    private function getProductData(): array
    {
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'sku' => trim($_POST['sku'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sale_type' => trim($_POST['sale_type'] ?? ''),
            'price' => $_POST['price'] ?? '',
            'vat_rate' => $_POST['vat_rate'] ?? '',
            'stock' => $_POST['stock'] ?? '',
            'image' => trim($_POST['image'] ?? ''),
            'origin' => trim($_POST['origin'] ?? ''),
            'is_active' => ($_POST['is_active'] ?? '1') === '1',
        ];
    }
}
