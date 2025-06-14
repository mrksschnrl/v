<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer (du brauchst composer)
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

function sendVerificationMail($toEmail, $toName, $token)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP-Einstellungen
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Absender & Empfänger
        $mail->setFrom(SUPPORT_EMAIL, 'videomat.org');
        $mail->addAddress($toEmail, $toName);

        // Inhalt der E-Mail
        $verifyLink = BASE_URL . "/login/verify.php?token=" . urlencode($token);
        $mail->isHTML(true);
        $mail->Subject = EMAIL_SUBJECT_VERIFY;
        $mail->Body    = "
            <h2>Willkommen bei videomat.org</h2>
            <p>Hallo <strong>$toName</strong>,</p>
            <p>bitte bestätige dein Konto über folgenden Link:</p>
            <p><a href='http://116.202.165.101$verifyLink'>$verifyLink</a></p>
            <p>Danke!<br>Das videomat.org-Team</p>
        ";
        $mail->AltBody = "Hallo $toName,\n\nBitte bestätige dein Konto:\nhttp://116.202.165.101$verifyLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
