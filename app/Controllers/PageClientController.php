<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
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
     * @throws JsonException
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
     * @throws JsonException
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
     * @throws JsonException
     */
    private function getCurrentClient(): array
    {
        $clientId = (int) (
            $_SESSION['client']['id']
            ?? 0
        );

        if ($clientId <= 0) {
            $this->redirect('/');
        }


        $clientModel = new Client();

        $client = $clientModel->find(
            $clientId
        );


        // Remove invalid or inactive sessions.
        if (
            !$client
            || (int) $client['is_active'] !== 1
        ) {
            unset($_SESSION['client']);

            session_regenerate_id(true);

            $this->redirect('/');
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
        $postalCode = trim($_POST['postal_code'] ?? '');
        $city = trim($_POST['city'] ?? '');


        // Validate profile data.
        if (
            $name === ''
            || $email === ''
            || !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->redirect(
                '/account/profile&error=1'
            );
        }


        $clientModel = new Client();

        $existingClient =
            $clientModel->findByEmail($email);


        // Prevent duplicate emails.
        if (
            $existingClient
            && (int) $existingClient['id']
                !== (int) $client['id']
        ) {
            $this->redirect(
                '/account/profile&email_exists=1'
            );
        }


        // Update profile.
        $clientModel->updateProfile(
            (int) $client['id'],
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone ?: null,
                'address' => [
                    'address' => $address,
                    'postal_code' => $postalCode,
                    'city' => $city,
                ],
            ]
        );


        // Keep the session synchronized.
        $_SESSION['client']['name'] = $name;
        $_SESSION['client']['email'] = $email;

        $this->redirect(
            '/account/profile&updated=1'
        );
    }

    /**
     * Show client orders.
     *
     * @return void
     * @throws JsonException
     */
    public function showOrders(): void
    {
        $client = $this->getCurrentClient();

        $saleModel = new Sale();

        $sales = $saleModel->getByClientId(
            (int) $client['id']
        );

        $this->view(
            'frontend/account/orders',
            [
                'client' => $client,
                'sales' => $sales,
            ]
        );
    }

    /**
     * Show a specific order.
     *
     * @return void
     * @throws JsonException
     */
    public function showOrder(): void
    {
        $client = $this->getCurrentClient();

        $saleId = (int) ($_GET['id'] ?? 0);

        if ($saleId <= 0) {
            $this->redirect(
                '/account/orders'
            );
        }


        $saleModel = new Sale();


        // Load only an order owned by this client.
        $sale = $saleModel->findByIdAndClientId(
            $saleId,
            (int) $client['id']
        );

        if (!$sale) {
            $this->redirect(
                '/account/orders'
            );
        }


        $items = $saleModel->getItemsBySaleId(
            $saleId
        );

        $this->view(
            'frontend/account/order',
            [
                'client' => $client,
                'sale' => $sale,
                'items' => $items,
            ]
        );
    }

    /**
     * Show client security settings.
     *
     * @return void
     * @throws JsonException
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
     * @throws JsonException
     */
    #[NoReturn]
    public function updatePassword(): void
    {
        $client = $this->getCurrentClient();

        $currentPassword =
            $_POST['current_password'] ?? '';

        $newPassword =
            $_POST['new_password'] ?? '';

        $confirmation =
            $_POST['password_confirmation'] ?? '';


        // Require all password fields.
        if (
            $currentPassword === ''
            || $newPassword === ''
            || $confirmation === ''
        ) {
            $this->redirect(
                '/account/security&error=1'
            );
        }


        // Verify current password.
        if (
            empty($client['password'])
            || !password_verify(
                $currentPassword,
                $client['password']
            )
        ) {
            $this->redirect(
                '/account/security&current_password=1'
            );
        }


        // Require at least 8 characters.
        if (strlen($newPassword) < 8) {
            $this->redirect(
                '/account/security&weak_password=1'
            );
        }


        // Verify confirmation.
        if ($newPassword !== $confirmation) {
            $this->redirect(
                '/account/security&mismatch=1'
            );
        }


        $clientModel = new Client();

        $clientModel->updatePassword(
            (int) $client['id'],
            $newPassword
        );


        // Renew the authenticated session.
        session_regenerate_id(true);

        $this->redirect(
            '/account/security&updated=1'
        );
    }

    /**
     * @return void
     * @throws JsonException
     */
    #[NoReturn]
    public function deactivateAccount(): void
    {
       $client = $this->getCurrentClient();

        $password =
            $_POST['password'] ?? '';


        // Verify account password.
        if (
            $password === ''
            || empty($client['password'])
            || !password_verify(
                $password,
                $client['password']
            )
        ) {
            $this->redirect(
                '/account/security&deactivate_error=1'
            );
        }


        $clientModel = new Client();

        $clientModel->setActive(
            (int) $client['id'],
            false
        );


        // End the client session.
        unset($_SESSION['client']);

        session_regenerate_id(true);

        $this->redirect(
            '/&account_deactivated=1'
        );
    }

    /**
     * Redirect to an application route.
     *
     * @param string $route
     * @return never
     */
    private function redirect(
        string $route
    ): never {
        header(
            'Location: /public/index.php?route='
            . $route
        );

        exit;
    }
}