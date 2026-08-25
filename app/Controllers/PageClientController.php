<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;
use Throwable;
use App\Models\Sale;

/**
 * Class PageClientController
 */
class PageClientController extends Controller
{

    /**
     * Display the client dashboard.
     *
     * @return void
     */
    public function index(): void
    {
        $client = $this->getCurrentClient();

        // Show client dashboard.
        $this->view('frontend/account/index', [
            'client' => $client,
        ]);
    }

    /**
     * Show client personal information.
     *
     * @return void
     */
    public function profile(): void
    {
        $client = $this->getCurrentClient();

        // Show client profile.
        $this->view('frontend/account/profile', [
            'client' => $client,
        ]);
    }

    /**
     * Load the authenticated client.
     *
     * @return array
     */
    private function getCurrentClient(): array
    {
        $clientId = (int) $_SESSION['client']['id'];

        $clientModel = new Client();
        $client = $clientModel->find($clientId);

        if (!$client) {
            unset($_SESSION['client']);

            header('Location: /public/index.php?route=/');
            exit;
        }

        return $client;
    }

    /**
     * Update client profile.
     *
     * @return void
     * @throws Throwable
     */
    #[NoReturn]
    public function updateProfile(): void
    {
        $client = $this->getCurrentClient();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validate required fields.
        if (
            $name === ''
            || $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            header(
                'Location: /public/index.php?route=/account/profile&error=1'
            );
            exit;
        }

        $clientModel = new Client();

        // Prevent duplicate email addresses.
        $existingClient = $clientModel->findByEmail($email);

        if (
            $existingClient
            && (int) $existingClient['id'] !== (int) $client['id']
        ) {
            header(
                'Location: /public/index.php?route=/account/profile&email_exists=1'
            );
            exit;
        }

        // Update profile.
        $clientModel->updateProfile(
            (int) $client['id'],
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone ?: null,
                'address' => $address ?: null,
            ]
        );

        // Keep session data synchronized.
        $_SESSION['client']['name'] = $name;
        $_SESSION['client']['email'] = $email;

        header(
            'Location: /public/index.php?route=/account/profile&updated=1'
        );
        exit;
    }

    /**
     * Show client orders.
     *
     * @return void
     */
    public function showOrders(): void
    {
        $client = $this->getCurrentClient();

        $saleModel = new Sale();

        $sales = $saleModel->getByClientId(
            (int) $client['id']
        );

        $this->view('frontend/account/orders', [
            'client' => $client,
            'sales' => $sales,
        ]);
    }

    /**
     * Show a specific order.
     *
     * @return void
     */
    public function showOrder(): void
    {
        $client = $this->getCurrentClient();
        $saleId = (int) ($_GET['id'] ?? 0);

        if ($saleId <= 0) {
            header('Location: /public/index.php?route=/account/orders');
            exit;
        }

        $saleModel = new Sale();

        // Load only an order owned by this client.
        $sale = $saleModel->findByIdAndClientId(
            $saleId,
            (int) $client['id']
        );

        if (!$sale) {
            header('Location: /public/index.php?route=/account/orders');
            exit;
        }

        // Load the products from this order.
        $items = $saleModel->getItemsBySaleId($saleId);

        $this->view('frontend/account/order', [
            'client' => $client,
            'sale' => $sale,
            'items' => $items,
        ]);
    }

    /**
     * Show client security settings.
     *
     * @return void
     */
    public function security(): void
    {
        $client = $this->getCurrentClient();

        $this->view('frontend/account/security', [
            'client' => $client,
        ]);
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function updatePassword(): void
    {
        $client = $this->getCurrentClient();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';

        // Validate required fields.
        if (
            $currentPassword === ''
            || $newPassword === ''
            || $confirmation === ''
        ) {
            header(
                'Location: /public/index.php?route=/account/security&error=1'
            );
            exit;
        }

        // Verify current password.
        if (
            empty($client['password'])
            || !password_verify(
                $currentPassword,
                $client['password']
            )
        ) {
            header(
                'Location: /public/index.php?route=/account/security&current_password=1'
            );
            exit;
        }

        // Require a reasonable password length.
        if (strlen($newPassword) < 8) {
            header(
                'Location: /public/index.php?route=/account/security&weak_password=1'
            );
            exit;
        }

        // Verify password confirmation.
        if ($newPassword !== $confirmation) {
            header(
                'Location: /public/index.php?route=/account/security&mismatch=1'
            );
            exit;
        }

        // Save the new password.
        $clientModel = new Client();

        $clientModel->updatePassword(
            (int) $client['id'],
            $newPassword
        );

        // Renew the authenticated session.
        session_regenerate_id(true);

        header(
            'Location: /public/index.php?route=/account/security&updated=1'
        );
        exit;
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function deactivateAccount(): void
    {
        $client = $this->getCurrentClient();

        $password = $_POST['password'] ?? '';

        // Confirm the client's password.
        if (
            $password === ''
            || empty($client['password'])
            || !password_verify($password, $client['password'])
        ) {
            header(
                'Location: /public/index.php?route=/account/security&deactivate_error=1'
            );
            exit;
        }

        $clientModel = new Client();

        // Deactivate the account.
        $clientModel->setActive(
            (int) $client['id'],
            false
        );

        // End the client session.
        unset($_SESSION['client']);

        session_regenerate_id(true);

        header(
            'Location: /public/index.php?route=/&account_deactivated=1'
        );
        exit;
    }
}