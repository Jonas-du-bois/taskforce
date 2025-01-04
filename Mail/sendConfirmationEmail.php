<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

function sendConfirmationEmail($prenom, $email, $token) {
    try {
        // Lien de confirmation
        $confirmationLink = "http://localhost/Taskforce/confirmation.php?token=" . urlencode($token);

        // Configuration de l'email
        $transport = Transport::fromDsn('smtp://localhost:1025');
        $mailer = new Mailer($transport);
        $message = (new Email())
            ->from('support@taskforce.com')
            ->to($email)
            ->subject('Confirmation de votre inscription')
            ->html("
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Confirmation d'inscription</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body style='background-color: #f8f9fa; font-family: Arial, sans-serif;'>
    <div class='container d-flex justify-content-center align-items-center' style='min-height: 100vh;'>
        <div class='card shadow-lg' style='max-width: 600px; width: 100%; border-radius: 10px;'>
            <div class='card-body p-4'>
                <h2 class='text-center mb-4' style='color: #0d6efd;'>Bienvenue sur TaskForce</h2>
                <p>Bonjour <strong>$prenom</strong>,</p>
                <p class='mb-4'>
                    Merci de vous être inscrit à notre plateforme ! Veuillez confirmer votre inscription pour activer votre compte et commencer à organiser vos tâches efficacement.
                </p>
                <div class='text-center mb-4'>
                    <a href='$confirmationLink' class='btn btn-primary' style='padding: 10px 20px; font-size: 16px;'>Confirmer mon inscription</a>
                </div>
                <p class='text-muted text-center' style='font-size: 14px;'>
                    Si vous n'avez pas demandé cette inscription, veuillez ignorer cet email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
        ");

        // Envoi de l'email
        $mailer->send($message);
    } catch (Exception $e) {
        echo "<p style='color: red;'>Une erreur est survenue lors de l'envoi de l'email : " . $e->getMessage() . "</p>";
    }
}

?>
