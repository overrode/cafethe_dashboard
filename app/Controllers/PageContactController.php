<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

/**
 * Class PageContactController
 * @package App\Controllers
 */
class PageContactController extends Controller
{
    /**
     * Display the contact page.
     *
     * @return void
     */
    public function index(): void
    {
        $contact_success = $_SESSION['contact_success'] ?? null;
        $contact_error = $_SESSION['contact_error'] ?? null;
        unset($_SESSION['contact_success']);
        unset($_SESSION['contact_error']);

        $this->view('frontend/contact/index', [
            'title' => 'Contact - CafThe',
            'contact_success' => $contact_success,
            'contact_error' => $contact_error,
        ]);
    }

    /**
     * Handle the contact form submission and send an email.
     *
     * @return void
     */
    public function sendEmail(): void
    {
        $contactName = trim($_POST['contact_name'] ?? '');
        $contactEmail = trim($_POST['contact_email'] ?? '');
        $contactMessage = trim($_POST['contact_message'] ?? '');
        $errors = [];

        if ($contactName === '') {
            $errors[] = 'Le nom est obligatoire.';
        }

        if ( $contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL) ) {
            $errors[] = 'Veuillez saisir une adresse e-mail valide.';
        }

        if ($contactMessage === '') {
            $errors[] = 'Le message est obligatoire.';
        }

        // Redisplay invalid form.
        if (!empty($errors)) {
            $this->view('frontend/contact/index', [
                'title' => 'Contact - CafThe',
                'errors' => $errors,
                'contact_name' => $contactName,
                'contact_email' => $contactEmail,
                'contact_message' => $contactMessage,
            ]);
            return;
        }

        try {
            $config = require __DIR__ . '/../../config/database.php';
            $mail = new PHPMailer(true);

            // Configure SMTP.
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $config['mail_host'];
            $mail->Username = $config['mail_username'];
            $mail->Password = $config['mail_password'];
            $mail->Port = $config['mail_port'];
            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Timeout = 10;


            // Configure sender and recipient.
            $mail->setFrom(
                $config['mail_from'],
                $config['mail_from_name']
            );

            $mail->addAddress(
                $config['mail_to']
            );

            $mail->addReplyTo(
                $contactEmail,
                $contactName
            );


            // Escape visitor content.
            $safeName = htmlspecialchars(
                $contactName,
                ENT_QUOTES,
                'UTF-8'
            );

            $safeEmail = htmlspecialchars(
                $contactEmail,
                ENT_QUOTES,
                'UTF-8'
            );

            $safeMessage = nl2br(
                htmlspecialchars(
                    $contactMessage,
                    ENT_QUOTES,
                    'UTF-8'
                )
            );


            // Build the email.
            $mail->isHTML(true);

            $mail->Subject =
                'Nouveau message depuis CafThé';

            $mail->Body = "
                <p><strong>Nom :</strong> {$safeName}</p>
                <p><strong>Email :</strong> {$safeEmail}</p>
                <p><strong>Message :</strong><br>{$safeMessage}</p>
            ";

            $mail->AltBody =
                "Nom : {$contactName}\n"
                . "Email : {$contactEmail}\n\n"
                . "Message :\n{$contactMessage}";


            $mail->send();


            $_SESSION['contact_success'] =
                'Votre message a été envoyé avec succès. '
                . 'Merci de nous avoir contactés.';
        } catch (Throwable $exception) {
             // Log the real mail error.
            Logger::exception(
                $exception,
                [
                    'controller' => self::class,
                    'action' => __FUNCTION__,
                    'contact_email' => $contactEmail,
                ]
            );

            $_SESSION['contact_error'] =
                'Une erreur est survenue lors de l\'envoi du message. '
                . 'Veuillez réessayer plus tard.';
        }
        header(
            'Location: /public/index.php?route=/contact'
        );

        exit;
    }
}