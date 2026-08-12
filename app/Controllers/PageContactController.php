<?php

namespace App\Controllers;

use App\Core\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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

        $config = require __DIR__ . '/../../config/database.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $config['mail_host'];
            $mail->Username = $config['mail_username'];
            $mail->Password = $config['mail_password'];
            $mail->Port = $config['mail_port'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom($config['mail_from'], $config['mail_from_name']);
            $mail->addAddress($config['mail_to']);
            $mail->addReplyTo($contactEmail, $contactName);

            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message depuis CafThe';

            $mail->Body ="
                Nom: {$contactName}<br>
                Email: {$contactEmail}<br>
                Message: {$contactMessage}
            ";

            // Debugging settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Debugoutput = 'html';
            $mail->Timeout = 10;

            $mail->send();

            $_SESSION['contact_success'] = 'Votre message a été envoyé avec succès. Merci de nous avoir contactés.';

            header('Location: /public/index.php?route=/contact');
            exit;
        } catch (Exception $e) {
            $_SESSION['contact_error'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.';

            header('Location: /public/index.php?route=/contact');
            exit;
        }
    }
}