<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Core\Auth;
use Throwable;

/**
 * Controller responsible for handling operations related to sales.
 */
class DashboardSaleController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        $saleModel = new Sale();
        $sales = $saleModel->all();

        $this->view('backend/sales/index', [
            'sales' => $sales,
        ]);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $clientModel = new Client();
        $productModel = new Product();

        $clients = $clientModel->all();
        $products = $productModel->getActiveProductsForSale();

        $this->view('backend/sales/create', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    /**
     * @return void
     */
    public function store(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            header('Location: /public/index.php?route=/login');
            exit;
        }

        $paymentMethod = $_POST['payment_method'] ?? '';
        $paymentReceived = ($_POST['payment_received'] ?? '') === '1';
        $deliveryMethod = $_POST['delivery_method'] ?? '';
        $immediateHandover = ($_POST['immediate_handover'] ?? '') === '1';

        $allowedPaymentMethods = [
            'cb',
            'especes',
            'cheque',
            'virement',
        ];

        $allowedDeliveryMethods = [
            'magasin',
            'livraison',
        ];

        if (
            !in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {
            $_SESSION['error'] = 'Mode de paiement invalide.';

            header('Location: /public/index.php?route=/dashboard/sales/create');
            exit;
        }

        if (
            !in_array(
                $deliveryMethod,
                $allowedDeliveryMethods,
                true
            )
        ) {
            $_SESSION['error'] = 'Mode de livraison invalide.';

            header('Location: /public/index.php?route=/dashboard/sales/create');
            exit;
        }


        /*
         * Derive the sale states from what actually happened.
         *
         * The seller does not choose internal database statuses directly.
         */
        if (!$paymentReceived) {
            /*
             * Order registered but not paid yet.
             */
            $status = 'pending';
            $paymentStatus = 'pending';
            $deliveryStatus = 'pending';

        } elseif (
            $deliveryMethod === 'magasin'
            && $immediateHandover
        ) {
            /*
             * Normal immediate counter sale.
             *
             * Paid and products have already been handed to the customer.
             */
            $status = 'completed';
            $paymentStatus = 'paid';
            $deliveryStatus = 'collected';

        } else {
            /*
             * Paid order but fulfillment still needs to happen.
             *
             * This covers:
             * - home delivery
             * - store pickup later
             */
            $status = 'preparing';
            $paymentStatus = 'paid';
            $deliveryStatus = 'pending';
        }


        $saleModel = new Sale();

        try {
            $saleModel->create([
                'user_id' => $userId,
                'client_id' => $_POST['client_id'] ?? null,

                'status' => $status,
                'payment_status' => $paymentStatus,
                'delivery_status' => $deliveryStatus,

                'payment_method' => $paymentMethod,
                'delivery_method' => $deliveryMethod,

                'source' => 'dashboard',

                'items' => $_POST['items'] ?? [],
            ]);

            $_SESSION['success'] = 'Vente enregistrée.';

        } catch (Throwable $exception) {
            $_SESSION['error'] = $exception->getMessage();

            header('Location: /public/index.php?route=/dashboard/sales/create');
            exit;
        }

        header('Location: /public/index.php?route=/dashboard/sales');
        exit;
    }

    /**
     * @return void
     */
    public function setPaid(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            header('Location: /public/index.php?route=/login');
            exit;
        }

        $saleId = (int) ($_POST['sale_id'] ?? 0);

        if ($saleId <= 0) {
            $_SESSION['error'] = 'Vente invalide.';

            header('Location: /public/index.php?route=/dashboard/sales');
            exit;
        }

        $saleModel = new Sale();

        try {
            $saleModel->setAsPaid($saleId);

            $_SESSION['success'] = 'Paiement confirmé.';
        } catch (Throwable $exception) {
            $_SESSION['error'] = $exception->getMessage();
        }

        header('Location: /public/index.php?route=/dashboard/sales');
        exit;
    }

    /**
     * @return void
     */
    public function setDeliveryStatus(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            header('Location: /public/index.php?route=/login');
            exit;
        }

        $saleId = (int) ($_POST['sale_id'] ?? 0);
        $deliveryStatus = $_POST['delivery_status'] ?? '';

        $saleModel = new Sale();

        try {
            $saleModel->setDeliveryStatus(
                $saleId,
                $deliveryStatus
            );

            $_SESSION['success'] = 'Statut de livraison mis à jour.';
        } catch (Throwable $exception) {
            $_SESSION['error'] = $exception->getMessage();
        }

        header('Location: /public/index.php?route=/dashboard/sales');
        exit;
    }

    /**
     * @return void
     */
    public function setCompleted(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            header('Location: /public/index.php?route=/login');
            exit;
        }

        $saleId = (int) ($_POST['sale_id'] ?? 0);

        $saleModel = new Sale();

        try {
            $saleModel->setAsCompleted($saleId);

            $_SESSION['success'] = 'Vente terminée.';
        } catch (Throwable $exception) {
            $_SESSION['error'] = $exception->getMessage();
        }

        header('Location: /public/index.php?route=/dashboard/sales');
        exit;
    }

    /**
     * @return void
     */
    public function setCancelled(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            header('Location: /public/index.php?route=/login');
            exit;
        }

        $saleId = (int) ($_POST['sale_id'] ?? 0);

        $saleModel = new Sale();

        try {
            $saleModel->setAsCancelled($saleId);

            $_SESSION['success'] = 'Vente annulée.';
        } catch (Throwable $exception) {
            $_SESSION['error'] = $exception->getMessage();
        }

        header('Location: /public/index.php?route=/dashboard/sales');
        exit;
    }
}
