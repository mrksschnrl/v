<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload und Config laden
// Autoload und Config laden
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../modules/config.php';
require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SUPPORT_EMAIL, 'videomat.org Testsystem');
    $mail->addAddress('mrksschnrl@gmail.com', 'Testempfänger'); // ⚠️ HIER ECHTE E-MAIL EINTRAGEN

    $mail->isHTML(true);
    $mail->Subject = 'Test: SMTP-Konfiguration funktioniert';
    $mail->Body    = '<p>Diese E-Mail wurde erfolgreich über den PHPMailer versendet.</p>';
    $mail->AltBody = 'Diese E-Mail wurde erfolgreich über den PHPMailer versendet.';

    $mail->send();
    echo 'Mail wurde erfolgreich gesendet.';
} catch (Exception $e) {
    echo 'Fehler beim Senden der Mail: ' . $mail->ErrorInfo;
}
